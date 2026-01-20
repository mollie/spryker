<?php

namespace Mollie\Service\Mollie\Url;

interface UrlResolverInterface
{
    /**
     * Calls UrlResolver class from client layer
     *
     * @api
     *
     * @param string $webhookUrl
     * @param string $testEnvironmentWebhookUrl
     * @param string $testMode
     *
     * @return string
     */
    public function resolveWebhookUrl(string $webhookUrl, string $testEnvironmentWebhookUrl, string $testMode): string;
}
