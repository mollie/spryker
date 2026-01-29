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
     * @param bool $testMode
     *
     * @return string
     */
    public function resolveWebhookUrl(string $webhookUrl, string $testEnvironmentWebhookUrl, bool $testMode): string;
}
