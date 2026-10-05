<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping;

use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;

interface ExpressCheckoutShippingOptionsProviderInterface
{
    /**
     * Specification:
     * - Puts the (partial) shipping address from Mollie's shipping callback on every item shipment of the quote.
     * - Returns the shipment methods available for that address as Mollie shipping options
     *   (reference = shipment method key, description = method name, amount = method price in the quote currency).
     * - Returns an unsuccessful response with an error when no method is available. Never throws.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer;
}
