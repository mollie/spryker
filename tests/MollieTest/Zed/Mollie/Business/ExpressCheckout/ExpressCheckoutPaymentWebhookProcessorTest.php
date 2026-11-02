<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieService;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Webhook\ExpressCheckoutPaymentWebhookProcessorInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Persistence\MollieEntityManager;
use Mollie\Zed\Mollie\Persistence\MollieRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutPaymentWebhookProcessorTest
 */
class ExpressCheckoutPaymentWebhookProcessorTest extends Unit
{
    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @var string
     */
    protected const TRANSACTION_ID = 'tr_QKZEBWM24ifxZzhqkGjXJ';

    /**
     * @var string
     */
    protected const REFUND_ID = 're_v76CeV8ChaYK5emGmGjXJ';

    /**
     * @var \Mollie\Client\Mollie\MollieClientInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected MollieClientInterface&MockObject $mollieClientMock;

    /**
     * @var array<\Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer>
     */
    protected array $savedMollieExpressCheckoutPaymentRefundTransfers = [];

    /**
     * @var array<\Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer>
     */
    protected array $mollieExpressCheckoutPaymentUpdateRequestTransfers = [];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mollieClientMock = $this->createMock(MollieClientInterface::class);
    }

    /**
     * @return void
     */
    public function testProcessUpdatesOrderPaymentWhenNoPaymentRefundExists(): void
    {
        $this->mollieClientMock->expects($this->never())->method('createRefund');

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentWebhookProcessor(null, true)
            ->process($this->createMolliePaymentTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertCount(1, $this->mollieExpressCheckoutPaymentUpdateRequestTransfers);
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0];
        $this->assertSame(static::EXPRESS_CHECKOUT_UUID, $mollieExpressCheckoutPaymentUpdateRequestTransfer->getExpressCheckoutUuid());
        $this->assertSame(static::TRANSACTION_ID, $mollieExpressCheckoutPaymentUpdateRequestTransfer->getTransactionId());
        $this->assertSame('paid', $mollieExpressCheckoutPaymentUpdateRequestTransfer->getStatus());
    }

    /**
     * @return void
     */
    public function testProcessReturnsNotFoundWhenNoPaymentRefundAndNoOrderPaymentExist(): void
    {
        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentWebhookProcessor(null, false)
            ->process($this->createMolliePaymentTransfer());

        $this->assertSame(404, $mollieWebhookResponseTransfer->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessRequestsRefundWhenPaymentRefundHasNoRefund(): void
    {
        $mollieRefundApiResponseTransfer = (new MollieRefundApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMollieRefund((new MollieRefundTransfer())->setId(static::REFUND_ID)->setStatus('pending'));
        $this->mollieClientMock->expects($this->once())->method('createRefund')->willReturn($mollieRefundApiResponseTransfer);

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentWebhookProcessor($this->createMollieExpressCheckoutPaymentRefundTransfer(), true)
            ->process($this->createMolliePaymentTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame(static::REFUND_ID, end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getRefundId());
        $this->assertSame('pending', $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0]->getStatus());
    }

    /**
     * @return void
     */
    public function testProcessUpdatesRefundStatusWhenPaymentRefundHasRefund(): void
    {
        $this->mollieClientMock->expects($this->never())->method('createRefund');
        $mollieExpressCheckoutPaymentRefundTransfer = $this->createMollieExpressCheckoutPaymentRefundTransfer()->setRefundId(static::REFUND_ID);
        $embeddedRefund = ['id' => static::REFUND_ID, 'status' => 'refunded'];
        $molliePaymentTransfer = $this->createMolliePaymentTransfer()->setEmbedded(['refunds' => [$embeddedRefund]]);

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentWebhookProcessor($mollieExpressCheckoutPaymentRefundTransfer, true)
            ->process($molliePaymentTransfer);

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame('refunded', end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getRefundStatus());
        $this->assertSame('refunded', $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0]->getStatus());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer|null $mollieExpressCheckoutPaymentRefundTransfer
     * @param bool $isOrderPaymentFound
     *
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Webhook\ExpressCheckoutPaymentWebhookProcessorInterface
     */
    protected function createExpressCheckoutPaymentWebhookProcessor(
        ?MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
        bool $isOrderPaymentFound,
    ): ExpressCheckoutPaymentWebhookProcessorInterface {
        $repositoryMock = $this->createMock(MollieRepository::class);
        $repositoryMock->method('findExpressCheckoutPaymentRefundByExpressCheckoutUuid')
            ->with(static::EXPRESS_CHECKOUT_UUID)
            ->willReturn($mollieExpressCheckoutPaymentRefundTransfer);

        $entityManagerMock = $this->createMock(MollieEntityManager::class);
        $entityManagerMock->method('updateExpressCheckoutPaymentRefund')->willReturnCallback(
            function (MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer) {
                $this->savedMollieExpressCheckoutPaymentRefundTransfers[] = clone $mollieExpressCheckoutPaymentRefundTransfer;

                return $mollieExpressCheckoutPaymentRefundTransfer;
            },
        );
        $entityManagerMock->method('updateExpressCheckoutMolliePayment')->willReturnCallback(
            function (MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer) use ($isOrderPaymentFound) {
                $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[] = $mollieExpressCheckoutPaymentUpdateRequestTransfer;

                return $isOrderPaymentFound;
            },
        );

        $businessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getMollieService'])
            ->getMock();
        $businessFactoryMock->method('getMollieClient')->willReturn($this->mollieClientMock);
        $businessFactoryMock->method('getMollieService')->willReturn(new MollieService());
        $businessFactoryMock->setRepository($repositoryMock);
        $businessFactoryMock->setEntityManager($entityManagerMock);

        return $businessFactoryMock->createExpressCheckoutPaymentWebhookProcessor();
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    protected function createMollieExpressCheckoutPaymentRefundTransfer(): MollieExpressCheckoutPaymentRefundTransfer
    {
        return (new MollieExpressCheckoutPaymentRefundTransfer())
            ->setIdMollieExpressCheckoutPaymentRefund(1)
            ->setExpressCheckoutUuid(static::EXPRESS_CHECKOUT_UUID)
            ->setMollieSessionId('sess_7behWYUFsjn5hwRB86mXJ');
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    protected function createMolliePaymentTransfer(): MolliePaymentTransfer
    {
        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('0.02');

        return (new MolliePaymentTransfer())
            ->setId(static::TRANSACTION_ID)
            ->setStatus('paid')
            ->setAmount($mollieAmountTransfer)
            ->setMetadata(['expressCheckoutUuid' => static::EXPRESS_CHECKOUT_UUID]);
    }
}
