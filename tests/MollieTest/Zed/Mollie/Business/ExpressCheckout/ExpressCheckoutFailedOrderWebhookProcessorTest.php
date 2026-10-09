<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder\ExpressCheckoutFailedOrderWebhookProcessor;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutFailedOrderWebhookProcessorTest
 */
class ExpressCheckoutFailedOrderWebhookProcessorTest extends Unit
{
    /**
     * @var string
     */
    protected const UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @var string
     */
    protected const TRANSACTION_ID = 'tr_QKZEBWM24ifxZzhqkGjXJ';

    /**
     * @var array<\Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer>
     */
    protected array $savedFailedOrders = [];

    /**
     * @return void
     */
    public function testNoFailedOrderRecordIsNotHandled(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->expects($this->never())->method('refund');

        $response = $this->createProcessor(null, false, $refunderMock)->process($this->createPayment('paid'));

        $this->assertFalse($response->getIsHandled());
    }

    /**
     * @return void
     */
    public function testPaymentWithoutExpressCheckoutUuidIsNotHandled(): void
    {
        $response = $this->createProcessor($this->createFailedOrder(), false, $this->createMock(ExpressCheckoutPaymentRefunderInterface::class))
            ->process($this->createPayment('paid')->setMetadata([]));

        $this->assertFalse($response->getIsHandled());
    }

    /**
     * @return void
     */
    public function testPaidPaymentIsUpdatedAndRefunded(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->expects($this->once())->method('refund')->willReturn(
            (new MollieRefundApiResponseTransfer())->setIsSuccessful(true)->setMollieRefund((new MollieRefundTransfer())->setId('re_v76CeV8ChaYK5emGmGjXJ')),
        );

        $response = $this->createProcessor($this->createFailedOrder(), false, $refunderMock)->process($this->createPayment('paid'));

        $this->assertTrue($response->getIsHandled());
        $this->assertSame(200, $response->getStatusCode());
        $savedFailedOrder = end($this->savedFailedOrders);
        $this->assertSame(static::TRANSACTION_ID, $savedFailedOrder->getTransactionId());
        $this->assertSame('paid', $savedFailedOrder->getStatus());
        $this->assertSame(2, $savedFailedOrder->getAmount());
        $this->assertSame('EUR', $savedFailedOrder->getCurrency());
        $this->assertSame('re_v76CeV8ChaYK5emGmGjXJ', $savedFailedOrder->getRefundId());
    }

    /**
     * @return void
     */
    public function testAlreadyRefundedPaymentIsNotRefundedAgain(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->expects($this->never())->method('refund');

        $response = $this->createProcessor($this->createFailedOrder()->setRefundId('re_x'), false, $refunderMock)
            ->process($this->createPayment('paid'));

        $this->assertTrue($response->getIsHandled());
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testNotPaidPaymentIsUpdatedButNotRefunded(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->expects($this->never())->method('refund');

        $response = $this->createProcessor($this->createFailedOrder(), false, $refunderMock)->process($this->createPayment('canceled'));

        $this->assertTrue($response->getIsHandled());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('canceled', end($this->savedFailedOrders)->getStatus());
    }

    /**
     * @return void
     */
    public function testFailedRefundReturnsErrorSoMollieRetries(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->method('refund')->willReturn((new MollieRefundApiResponseTransfer())->setIsSuccessful(false)->setMessage('Refund rejected'));

        $response = $this->createProcessor($this->createFailedOrder(), false, $refunderMock)->process($this->createPayment('paid'));

        $this->assertTrue($response->getIsHandled());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertNull(end($this->savedFailedOrders)->getRefundId());
    }

    /**
     * @return void
     */
    public function testPaymentBelongingToAnOrderIsNeverRefunded(): void
    {
        $refunderMock = $this->createMock(ExpressCheckoutPaymentRefunderInterface::class);
        $refunderMock->expects($this->never())->method('refund');

        $response = $this->createProcessor($this->createFailedOrder(), true, $refunderMock)->process($this->createPayment('paid'));

        $this->assertFalse($response->getIsHandled());
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer|null $failedOrder
     * @param bool $hasMolliePayment
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface&\PHPUnit\Framework\MockObject\MockObject $refunderMock
     *
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder\ExpressCheckoutFailedOrderWebhookProcessor
     */
    protected function createProcessor(
        ?MollieExpressCheckoutFailedOrderTransfer $failedOrder,
        bool $hasMolliePayment,
        ExpressCheckoutPaymentRefunderInterface&MockObject $refunderMock,
    ): ExpressCheckoutFailedOrderWebhookProcessor {
        $repositoryMock = $this->createMock(MollieRepositoryInterface::class);
        $repositoryMock->method('findExpressCheckoutFailedOrderByExpressCheckoutUuid')->with(static::UUID)->willReturn($failedOrder);
        $repositoryMock->method('hasMolliePaymentByExpressCheckoutUuid')->willReturn($hasMolliePayment);

        $entityManagerMock = $this->createMock(MollieEntityManagerInterface::class);
        $entityManagerMock->method('updateExpressCheckoutFailedOrder')->willReturnCallback(
            function (MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer) {
                $this->savedFailedOrders[] = clone $mollieExpressCheckoutFailedOrderTransfer;

                return $mollieExpressCheckoutFailedOrderTransfer;
            },
        );

        return new ExpressCheckoutFailedOrderWebhookProcessor($repositoryMock, $entityManagerMock, $refunderMock);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    protected function createFailedOrder(): MollieExpressCheckoutFailedOrderTransfer
    {
        return (new MollieExpressCheckoutFailedOrderTransfer())
            ->setIdMollieExpressCheckoutFailedOrder(1)
            ->setExpressCheckoutUuid(static::UUID)
            ->setMollieSessionId('sess_7behWYUFsjn5hwRB86mXJ')
            ->setStatus('order_failed');
    }

    /**
     * @param string $status
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    protected function createPayment(string $status): MolliePaymentTransfer
    {
        return (new MolliePaymentTransfer())
            ->setId(static::TRANSACTION_ID)
            ->setStatus($status)
            ->setAmount((new MollieAmountTransfer())->setCurrency('EUR')->setValue('0.02'))
            ->setMetadata(['expressCheckoutUuid' => static::UUID]);
    }
}
