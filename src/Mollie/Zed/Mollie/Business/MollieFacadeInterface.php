<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieExpirationInformationTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

interface MollieFacadeInterface
{
    /**
     * Specification:
     * - Updates payment status in database based on Mollie payment data
     * - Triggers appropriate OMS state machine event
     * - Returns processing result
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterActiveMolliePaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int, mixed> $orderItems
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function mapOrderItemsToOrderTransfer(OrderTransfer $orderTransfer, array $orderItems): OrderTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function capturePayment(
        MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer,
    ): MolliePaymentCaptureResponseTransfer;

     /**
      * @param int $idSalesOrder
      *
      * @return void
      */
    public function releaseAuthorization(int $idSalesOrder): void;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationFailed(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationCanceled(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationExpired(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorized(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptured(int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptureFailed(int $idSalesOrderItem): bool;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendPaymentConfirmationMail(OrderTransfer $orderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function createPaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function processPaymentLinkData(OrderTransfer $orderTransfer): MolliePaymentLinkTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkCreationSuccessful(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkStatusPaid(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isPaymentLinkStatusExpired(int $idSalesOrder): bool;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentLinkTransfer $molliePaymentLinkTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    public function updatePaymentLink(MolliePaymentLinkTransfer $molliePaymentLinkTransfer): MolliePaymentLinkTransfer;

    /**
     * @param int $orderId
     *
     * @return \Generated\Shared\Transfer\MollieExpirationInformationTransfer
     */
    public function getExpirationInformation(int $orderId): MollieExpirationInformationTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer): MolliePaymentMethodConfigCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null
     */
    public function getPaymentMethodConfigByCriteria(
        MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer,
    ): ?MolliePaymentMethodConfigTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer
     */
    public function writeMolliePaymentConfigData(MolliePaymentMethodConfigTransfer $molliePaymentMethodConfigTransfer): MolliePaymentMethodConfigTransfer;

    /**
     * @param int $mollieId
     *
     * @return void
     */
    public function deleteMolliePaymentConfigData(int $mollieId): void;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer|null
     */
    public function getMolliePaymentLinkByIdSalesOrder(int $idSalesOrder): ?MolliePaymentLinkTransfer;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer
     */
    public function getExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCriteriaTransfer $mollieExpressCheckoutConfigCriteriaTransfer,
    ): MollieExpressCheckoutConfigCollectionTransfer;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer
     *
     * @return void
     */
    public function saveExpressCheckoutConfigCollection(
        MollieExpressCheckoutConfigCollectionTransfer $mollieExpressCheckoutConfigCollectionTransfer,
    ): void;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer,
    ): void;

    /**
     * Specification:
     * - Places a Spryker order for an express checkout (Apple Pay, PayPal via the Mollie express component).
     * - Sets the billing and shipping address of the request on the quote; both are required.
     * - Assigns the shipment method with the request's `shipmentMethodKey` and adds its expense.
     * - Sets the Mollie express payment (`mollieExpressPayment`) with the request's `expressCheckoutUuid`;
     *   MollieExpressCheckoutDoSaveOrderPlugin stores it in spy_payment_mollie within the order save transaction.
     * - Recalculates the quote, requires the grand total to equal the request's `expectedGrandTotal` and places the order via the Checkout facade.
     * - Returns `isSuccessful` true with `quote` and `orderReference` when the checkout response is successful.
     * - Returns `isSuccessful` false with `errors` when the checkout response is not successful, or when preparing the quote or the checkout throws (never throws).
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeExpressCheckoutOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer;

    /**
     * Specification:
     * - Returns the shipment methods available for the quote as Mollie shipping options, declared on the
     *   express checkout session so the shopper can pick one in the express sheet.
     * - Returns `isSuccessful` and `options` (reference = shipment method key, description, amount), or `error`. Never throws.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getExpressCheckoutShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer;

    /**
     * Specification:
     * - Saves the record of an express checkout payment that has to be refunded, because placing the order failed after the shopper paid.
     * - Stores the express checkout uuid and the Mollie session id.
     * - The payment webhook requests the refund later, see processExpressCheckoutPaymentWebhook().
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    public function createExpressCheckoutPaymentRefund(
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieExpressCheckoutPaymentRefundTransfer;

    /**
     * Specification:
     * - Processes the payment webhook of an express checkout payment.
     * - Without a payment refund record for the payment's express checkout uuid, stores the payment id and status on the order's spy_payment_mollie row.
     * - Returns status code 404 when no such row exists yet, so Mollie retries.
     * - With a payment refund record that has no refund id, stores the payment id, status, amount and currency on the record and fully refunds a paid payment.
     * - After the refund is created, stores the refund's status on the spy_payment_mollie row of the order, when an order exists for the express checkout uuid.
     * - Returns status code 500 when the refund could not be created, so Mollie retries.
     * - With a payment refund record that has a refund id, stores the status of that refund on the record and on the spy_payment_mollie row of the order.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function processExpressCheckoutPaymentWebhook(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer;
}
