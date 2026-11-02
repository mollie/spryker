<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;

interface ExpressCheckoutOrderPlacerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeOrder(
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderResponseTransfer;
}
