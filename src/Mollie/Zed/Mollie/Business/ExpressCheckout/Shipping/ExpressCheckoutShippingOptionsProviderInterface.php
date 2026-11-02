<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Shipping;

use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer;

interface ExpressCheckoutShippingOptionsProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutShippingOptionsResponseTransfer
     */
    public function getShippingOptions(
        MollieExpressCheckoutShippingOptionsRequestTransfer $mollieExpressCheckoutShippingOptionsRequestTransfer,
    ): MollieExpressCheckoutShippingOptionsResponseTransfer;
}
