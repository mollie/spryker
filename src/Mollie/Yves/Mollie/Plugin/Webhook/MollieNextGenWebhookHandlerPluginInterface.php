<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MollieWebhookEventTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;

interface MollieNextGenWebhookHandlerPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieWebhookEventTransfer $mollieWebhookEventTransfer
     *
     * @return bool
     */
    public function isApplicable(MollieWebhookEventTransfer $mollieWebhookEventTransfer): bool;

    /**
     * @param \Generated\Shared\Transfer\MollieWebhookEventTransfer $mollieWebhookEventTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MollieWebhookEventTransfer $mollieWebhookEventTransfer): MollieWebhookResponseTransfer;
}
