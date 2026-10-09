<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;

interface ExpressCheckoutFailedOrderWebhookProcessorInterface
{
    /**
     * Specification:
     * - Finds the failed order record by the express checkout uuid from the payment metadata.
     * - Not handled (`isHandled` false) when there is no failed order record or the payment belongs to an order.
     * - Otherwise updates the record with the payment data and fully refunds a paid payment once.
     * - Returns the status code and message for Mollie; a failed refund returns an error code, so Mollie retries.
     *
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function process(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer;
}
