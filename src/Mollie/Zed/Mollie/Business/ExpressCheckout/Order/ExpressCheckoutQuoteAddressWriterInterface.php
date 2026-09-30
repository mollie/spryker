<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutQuoteResponseTransfer;

interface ExpressCheckoutQuoteAddressWriterInterface
{
    /**
     * Specification:
     * - Sets the request addresses on the quote and assigns the first available shipment method.
     * - Recalculates, so the grand total includes shipping before the Mollie session is created.
     * - Never throws: failures are returned as an unsuccessful response with error messages.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutQuoteResponseTransfer
     */
    public function writeAddresses(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutQuoteResponseTransfer;
}
