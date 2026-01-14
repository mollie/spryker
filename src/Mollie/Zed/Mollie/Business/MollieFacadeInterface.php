<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
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
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer
     */
    public function getAvailablePaymentMethods(MollieApiRequestTransfer $requestTransfer): MollieAvailablePaymentMethodsApiResponseTransfer;
}
