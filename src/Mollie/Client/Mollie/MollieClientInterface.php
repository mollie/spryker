<?php
declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundRequestTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;

interface MollieClientInterface
{
    /**
     *  Specification:
     *  - Gets list of enabled payment methods from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getEnabledPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer;

    /**
     *  Specification:
     *  - Gets list of all payment methods from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getAllPaymentMethods(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer;

    /**
     * Specification:
     * - Gets payment by transaction id from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function getPaymentByTransactionId(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer;

    /**
     * Specification:
     * - Creates a payment in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    public function createPayment(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentApiResponseTransfer;

    /**
     * Specification:
     * - Creates a refund in Mollie system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function createRefund(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieRefundApiResponseTransfer;

    /**
     * Specification:
     * - Gets refund by refund id from Mollie
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function getRefundByRefundId(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieRefundApiResponseTransfer;

    /**
     * Specification:
     * - Gets persisted refund by refund id
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieRefundRequestTransfer $mollieRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function getPersistedRefundById(MollieRefundRequestTransfer $mollieRefundRequestTransfer): MollieRefundResponseTransfer;

    /**
     * Specification:
     *  - Updates payment status in mollie record
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer;

    /**
     * Specification:
     * - Deletes cache for enabled payment methods API
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteEnabledPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void;

    /**
     * Specification:
     * - Deletes cache for all payment methods API
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteAllPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void;
}
