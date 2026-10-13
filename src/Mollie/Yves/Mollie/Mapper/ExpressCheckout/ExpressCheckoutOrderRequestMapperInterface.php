<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Mapper\ExpressCheckout;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;

interface ExpressCheckoutOrderRequestMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer
     */
    public function mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
        MollieExpressCheckoutSessionTransfer $mollieExpressCheckoutSessionTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): MollieExpressCheckoutOrderRequestTransfer;
}
