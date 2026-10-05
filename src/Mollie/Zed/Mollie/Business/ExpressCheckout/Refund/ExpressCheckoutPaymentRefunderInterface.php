<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutRefundResponseTransfer;

interface ExpressCheckoutPaymentRefunderInterface
{
    /**
     * Specification:
     * - Refunds the full amount of the express checkout payment for which no order could be created.
     * - The payment is identified by the express checkout uuid; its transaction id comes from the payment webhook.
     * - Returns `isPaymentKnown = false` when the webhook has not arrived yet (caller may retry).
     * - Idempotent per uuid (Mollie idempotency key), so a repeated call does not refund twice.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutRefundResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutRefundResponseTransfer;
}
