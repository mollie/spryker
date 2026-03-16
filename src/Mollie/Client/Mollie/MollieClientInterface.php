<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
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
     *
     * - Captures the payment
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer
     */
    public function createCapture(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieCreateCaptureApiResponseTransfer;

    /**
     *  Specification:
     *
     *  - Gets capture payment details
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer
     */
    public function getCapture(MollieApiRequestTransfer $mollieApiRequestTransfer): MollieGetCaptureApiResponseTransfer;

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

    /**
     * Specification:
     * - Gets current profile from Mollie
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MollieGetProfileApiResponseTransfer
     */
    public function getCurrentProfile(): MollieGetProfileApiResponseTransfer;

    /**
     * Specification:
     * - Processes refund data
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer;

    /**
     * Specification:
     * - Logs Mollie API response
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $mollieLogApiTransfer
     *
     * @return void
     */
    public function logMessage(MollieLogApiTransfer $mollieLogApiTransfer): void;

    /**
     * Specification:
     * - updates payment capture collection
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): void;

    /**
     * Specification:
     * - Creates new payment link by sending a request to Mollie API
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function createPaymentLink(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentLinkApiResponseTransfer;

    /**
     * Specification:
     * - Gets payment links from Mollie API
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkApiResponseTransfer
     */
    public function getPaymentLinks(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentLinkApiResponseTransfer;
}
