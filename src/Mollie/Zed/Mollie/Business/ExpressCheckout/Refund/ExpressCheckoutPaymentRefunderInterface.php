<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;

interface ExpressCheckoutPaymentRefunderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieRefundApiResponseTransfer;
}
