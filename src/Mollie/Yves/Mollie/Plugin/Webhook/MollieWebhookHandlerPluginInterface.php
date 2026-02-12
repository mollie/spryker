<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;

interface MollieWebhookHandlerPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return bool
     */
    public function isApplicable(MolliePaymentTransfer $molliePaymentTransfer): bool;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer;
}
