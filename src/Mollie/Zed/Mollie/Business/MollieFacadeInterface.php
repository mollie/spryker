<?php


declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieExpirationInformationTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
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
use Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer;
use Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer;
use Generated\Shared\Transfer\MollieSingleClickConfigTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer;
use Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

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
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer,
    ): void;

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer;

    /**
     * Specification:
     * - Returns the single-click setting saved in the Back Office.
     * - Falls back to `MollieConstants::MOLLIE_SINGLE_CLICK_PAYMENTS_ENABLED` when nothing was saved.
     *
     * @api
     *
     * @return bool
     */
    public function isSingleClickPaymentEnabled(): bool;

    /**
     * Specification:
     * - Saves the single-click setting from the Back Office; it overrides the config value.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer
     *
     * @return void
     */
    public function saveSingleClickConfig(MollieSingleClickConfigTransfer $mollieSingleClickConfigTransfer): void;

    /**
     * Specification:
     * - Returns whether single-click payments are enabled.
     * - When enabled, returns the saved cards (valid credit card mandates) of the given customer.
     * - Removes a stored Mollie customer that Mollie no longer knows (404/410) and returns no saved cards.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getSingleClickPaymentData(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer;

    /**
     * Specification:
     * - Returns whether single-click payments are enabled.
     * - Returns the saved cards (valid credit card mandates) of the given customer, also when single-click payments
     *   are disabled, so the customer can still remove them.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSingleClickPaymentDataTransfer
     */
    public function getCustomerSavedCards(
        MollieSingleClickPaymentDataRequestTransfer $mollieSingleClickPaymentDataRequestTransfer,
    ): MollieSingleClickPaymentDataTransfer;

    /**
     * Specification:
     * - Requires `MollieSavedCardRemoveRequestTransfer.idCustomer` and `MollieSavedCardRemoveRequestTransfer.mandateId`.
     * - Checks that the mandate is one of the customer's current saved cards at Mollie.
     * - Revokes the mandate at Mollie, so it can no longer be charged and disappears from the saved cards.
     * - Returns `isSuccessful=false` when the card does not belong to the customer or Mollie rejects the revoke.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieSavedCardRemoveResponseTransfer
     */
    public function removeSavedCard(
        MollieSavedCardRemoveRequestTransfer $mollieSavedCardRemoveRequestTransfer,
    ): MollieSavedCardRemoveResponseTransfer;
}
