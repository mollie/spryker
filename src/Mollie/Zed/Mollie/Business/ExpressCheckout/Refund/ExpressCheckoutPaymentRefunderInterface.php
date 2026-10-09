<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;

interface ExpressCheckoutPaymentRefunderInterface
{
    /**
     * Specification:
     * - Fully refunds the express checkout payment of an order that could not be created.
     * - Uses the express checkout uuid as idempotency key, so the payment is refunded only once.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieRefundApiResponseTransfer;
}
