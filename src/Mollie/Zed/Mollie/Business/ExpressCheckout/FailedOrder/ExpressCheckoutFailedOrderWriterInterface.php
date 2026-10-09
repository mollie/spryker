<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;

interface ExpressCheckoutFailedOrderWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    public function create(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer;
}
