<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;

interface ExpressCheckoutOrderHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param string $expressCheckoutUuid
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer
     */
    public function placeOrder(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        string $expressCheckoutUuid,
    ): MollieExpressCheckoutOrderResponseTransfer;
}
