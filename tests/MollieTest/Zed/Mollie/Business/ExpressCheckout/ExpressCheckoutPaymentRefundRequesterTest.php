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
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundRequesterInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Persistence\MollieEntityManager;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutPaymentRefundRequesterTest
 */
class ExpressCheckoutPaymentRefundRequesterTest extends Unit
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
    public function testRequestRefundStoresPaymentAndRefundsPaidPayment(): void
    {
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setId(static::REFUND_ID)
            ->setStatus('pending');
        $mollieRefundApiResponseTransfer = (new MollieRefundApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMollieRefund($mollieRefundTransfer);
        $this->mollieClientMock->expects($this->once())->method('createRefund')->willReturn($mollieRefundApiResponseTransfer);

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundRequester()
            ->requestRefund($this->createMolliePaymentTransfer('paid'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());

        $savedMollieExpressCheckoutPaymentRefundTransfer = end($this->savedMollieExpressCheckoutPaymentRefundTransfers);
        $this->assertSame(static::TRANSACTION_ID, $savedMollieExpressCheckoutPaymentRefundTransfer->getTransactionId());
        $this->assertSame('paid', $savedMollieExpressCheckoutPaymentRefundTransfer->getPaymentStatus());
        $this->assertSame(2, $savedMollieExpressCheckoutPaymentRefundTransfer->getAmount());
        $this->assertSame('EUR', $savedMollieExpressCheckoutPaymentRefundTransfer->getCurrency());
        $this->assertSame(static::REFUND_ID, $savedMollieExpressCheckoutPaymentRefundTransfer->getRefundId());
        $this->assertSame('pending', $savedMollieExpressCheckoutPaymentRefundTransfer->getRefundStatus());
    }

    /**
     * @return void
     */
    public function testRequestRefundStoresRefundStatusOnOrderPaymentAfterRefundIsCreated(): void
    {
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setId(static::REFUND_ID)
            ->setStatus('pending');
        $mollieRefundApiResponseTransfer = (new MollieRefundApiResponseTransfer())
            ->setIsSuccessful(true)
            ->setMollieRefund($mollieRefundTransfer);
        $this->mollieClientMock->method('createRefund')->willReturn($mollieRefundApiResponseTransfer);

        $this->createExpressCheckoutPaymentRefundRequester()
            ->requestRefund($this->createMolliePaymentTransfer('paid'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertCount(1, $this->mollieExpressCheckoutPaymentUpdateRequestTransfers);
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0];
        $this->assertSame(static::EXPRESS_CHECKOUT_UUID, $mollieExpressCheckoutPaymentUpdateRequestTransfer->getExpressCheckoutUuid());
        $this->assertSame(static::TRANSACTION_ID, $mollieExpressCheckoutPaymentUpdateRequestTransfer->getTransactionId());
        $this->assertSame('pending', $mollieExpressCheckoutPaymentUpdateRequestTransfer->getStatus());
    }

    /**
     * @return void
     */
    public function testRequestRefundStoresPaymentButDoesNotRefundNotPaidPayment(): void
    {
        $this->mollieClientMock->expects($this->never())->method('createRefund');

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundRequester()
            ->requestRefund($this->createMolliePaymentTransfer('canceled'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame('canceled', end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getPaymentStatus());
    }

    /**
     * @return void
     */
    public function testRequestRefundReturnsErrorStatusCodeWhenRefundIsNotCreated(): void
    {
        $mollieRefundApiResponseTransfer = (new MollieRefundApiResponseTransfer())
            ->setIsSuccessful(false)
            ->setMessage('Refund rejected');
        $this->mollieClientMock->method('createRefund')->willReturn($mollieRefundApiResponseTransfer);

        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundRequester()
            ->requestRefund($this->createMolliePaymentTransfer('paid'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(500, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertNull(end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getRefundId());
        $this->assertSame([], $this->mollieExpressCheckoutPaymentUpdateRequestTransfers);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundRequesterInterface
     */
    protected function createExpressCheckoutPaymentRefundRequester(): ExpressCheckoutPaymentRefundRequesterInterface
    {
        $entityManagerMock = $this->createMock(MollieEntityManager::class);
        $entityManagerMock->method('updateExpressCheckoutPaymentRefund')->willReturnCallback(
            function (MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer) {
                $this->savedMollieExpressCheckoutPaymentRefundTransfers[] = clone $mollieExpressCheckoutPaymentRefundTransfer;

                return $mollieExpressCheckoutPaymentRefundTransfer;
            },
        );
        $entityManagerMock->method('updateExpressCheckoutMolliePayment')->willReturnCallback(
            function (MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer) {
                $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[] = $mollieExpressCheckoutPaymentUpdateRequestTransfer;

                return true;
            },
        );

        $businessFactoryMock = $this->getMockBuilder(MollieBusinessFactory::class)
            ->onlyMethods(['getMollieClient', 'getMollieService'])
            ->getMock();
        $businessFactoryMock->method('getMollieClient')->willReturn($this->mollieClientMock);
        $businessFactoryMock->method('getMollieService')->willReturn(new MollieService());
        $businessFactoryMock->setEntityManager($entityManagerMock);

        return $businessFactoryMock->createExpressCheckoutPaymentRefundRequester();
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
     * @param string $paymentStatus
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    protected function createMolliePaymentTransfer(string $paymentStatus): MolliePaymentTransfer
    {
        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('0.02');

        return (new MolliePaymentTransfer())
            ->setId(static::TRANSACTION_ID)
            ->setStatus($paymentStatus)
            ->setAmount($mollieAmountTransfer)
            ->setMetadata(['expressCheckoutUuid' => static::EXPRESS_CHECKOUT_UUID]);
    }
}
