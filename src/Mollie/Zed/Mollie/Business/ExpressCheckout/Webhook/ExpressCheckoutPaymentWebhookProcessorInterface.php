<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;

interface ExpressCheckoutPaymentWebhookProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function process(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer;
}
