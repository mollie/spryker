<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundStatusUpdaterInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Mollie\Zed\Mollie\Persistence\MollieEntityManager;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutPaymentRefundStatusUpdaterTest
 */
class ExpressCheckoutPaymentRefundStatusUpdaterTest extends Unit
{
    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '19d863d2-5eaf-4c8c-91f6-e1b9329c6c2d';

    /**
     * @var string
     */
    protected const REFUND_ID = 're_v76CeV8ChaYK5emGmGjXJ';

    /**
     * @var string
     */
    protected const OTHER_REFUND_ID = 're_4qqhO89gsT';

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
    public function testUpdateRefundStatusStoresRefundedStatus(): void
    {
        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundStatusUpdater()
            ->updateRefundStatus($this->createMolliePaymentTransfer(static::REFUND_ID, 'refunded'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame('refunded', end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getRefundStatus());
        $this->assertSame('refunded', $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0]->getStatus());
    }

    /**
     * @return void
     */
    public function testUpdateRefundStatusStoresFailedStatus(): void
    {
        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundStatusUpdater()
            ->updateRefundStatus($this->createMolliePaymentTransfer(static::REFUND_ID, 'failed'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame('failed', end($this->savedMollieExpressCheckoutPaymentRefundTransfers)->getRefundStatus());
        $this->assertSame('failed', $this->mollieExpressCheckoutPaymentUpdateRequestTransfers[0]->getStatus());
    }

    /**
     * @return void
     */
    public function testUpdateRefundStatusIgnoresRefundOfAnotherRefundId(): void
    {
        $mollieWebhookResponseTransfer = $this->createExpressCheckoutPaymentRefundStatusUpdater()
            ->updateRefundStatus($this->createMolliePaymentTransfer(static::OTHER_REFUND_ID, 'refunded'), $this->createMollieExpressCheckoutPaymentRefundTransfer());

        $this->assertSame(200, $mollieWebhookResponseTransfer->getStatusCode());
        $this->assertSame([], $this->savedMollieExpressCheckoutPaymentRefundTransfers);
        $this->assertSame([], $this->mollieExpressCheckoutPaymentUpdateRequestTransfers);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundStatusUpdaterInterface
     */
    protected function createExpressCheckoutPaymentRefundStatusUpdater(): ExpressCheckoutPaymentRefundStatusUpdaterInterface
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

        $businessFactory = new MollieBusinessFactory();
        $businessFactory->setEntityManager($entityManagerMock);

        return $businessFactory->createExpressCheckoutPaymentRefundStatusUpdater();
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    protected function createMollieExpressCheckoutPaymentRefundTransfer(): MollieExpressCheckoutPaymentRefundTransfer
    {
        return (new MollieExpressCheckoutPaymentRefundTransfer())
            ->setIdMollieExpressCheckoutPaymentRefund(1)
            ->setExpressCheckoutUuid(static::EXPRESS_CHECKOUT_UUID)
            ->setMollieSessionId('sess_7behWYUFsjn5hwRB86mXJ')
            ->setTransactionId('tr_QKZEBWM24ifxZzhqkGjXJ')
            ->setPaymentStatus('paid')
            ->setRefundId(static::REFUND_ID);
    }

    /**
     * @param string $refundId
     * @param string $refundStatus
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    protected function createMolliePaymentTransfer(string $refundId, string $refundStatus): MolliePaymentTransfer
    {
        $embeddedRefund = ['id' => $refundId, 'status' => $refundStatus];

        return (new MolliePaymentTransfer())
            ->setId('tr_QKZEBWM24ifxZzhqkGjXJ')
            ->setStatus('paid')
            ->setMetadata(['expressCheckoutUuid' => static::EXPRESS_CHECKOUT_UUID])
            ->setEmbedded(['refunds' => [$embeddedRefund]]);
    }
}
