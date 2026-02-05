<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Symfony\Component\HttpFoundation\Request;

interface MollieWebhookHandlerPluginInterface
{
    /**
     * Specification:
     * - Checks if this plugin can handle the webhook request.
     * - Returns true if the plugin is applicable for the given request.
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function isApplicable(Request $request): bool;

    /**
     * Specification:
     * - Processes the webhook request.
     * - Returns response transfer with status code and message.
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(Request $request): MollieWebhookResponseTransfer;
}
