<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;

interface ExpressCheckoutOrderPlacerInterface
{
    /**
     * Specification:
     * - Prepares the request quote with the configured quote expanders (addresses, shipment method, payment).
     * - Recalculates the quote and sets the payment amount to the grand total.
     * - Places the order through the Checkout facade.
     * - On success returns the placed quote, `orderReference` and `idSalesOrder`.
     * - Never throws: failures are returned as an unsuccessful response with error messages.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer;
}
