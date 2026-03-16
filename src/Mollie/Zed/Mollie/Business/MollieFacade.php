<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieBusinessFactory getFactory()
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 */
class MollieFacade extends AbstractFacade implements MollieFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        return $this->getFactory()
            ->createPaymentStatusUpdater()
            ->updateOrderCollection($updateOrderCollectionRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        return $this->getFactory()->createMolliePaymentHandler()->createPayment($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterActiveMolliePaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer
    {
        return $this->getFactory()->createMolliePaymentMethodsFilter()->applyFilter($paymentMethodsTransfer, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer
    {
        return $this->getFactory()
            ->createRefundProcessor()
            ->processOrderItemsRefund($orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int, mixed> $orderItems
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function mapOrderItemsToOrderTransfer(OrderTransfer $orderTransfer, array $orderItems): OrderTransfer
    {
        return $this->getFactory()
            ->createOrderMapper()
            ->mapOrderItemsToOrderTransfer($orderTransfer, $orderItems);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        return $this->getFactory()->createRefundProcessor()->processRefundData($molliePaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function capturePayment(
        MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer,
    ): MolliePaymentCaptureResponseTransfer {
        return $this->getFactory()->createMolliePaymentCaptureRequestSender()->capturePayment($molliePaymentCaptureRequestTransfer);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function releaseAuthorization(int $idSalesOrder): void
    {
        $this->getFactory()->createMollieReleaseAuthorizationRequestSender()->releaseAuthorization($idSalesOrder);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationFailed(int $idSalesOrder): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isAuthorizationFailed($idSalesOrder);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationCanceled(int $idSalesOrder): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isAuthorizationCanceled($idSalesOrder);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationExpired(int $idSalesOrder): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isAuthorizationExpired($idSalesOrder);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorized(int $idSalesOrder): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isAuthorized($idSalesOrder);
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptured(int $idSalesOrderItem): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isCaptured($idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptureFailed(int $idSalesOrderItem): bool
    {
        return $this->getFactory()->createMolliePaymentStatusHandler()->isCaptureFailed($idSalesOrderItem);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer
    {
        return $this->getFactory()->createCaptureProcessor()->updatePaymentCaptureCollection($molliePaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendPaymentConfirmationMail(OrderTransfer $orderTransfer): void
    {
        $this->getFactory()->createMailHandler()->sendPaymentConfirmationMail($orderTransfer);
    }
}
