<?php

namespace Mollie\Service\Mollie\Url;

class UrlResolver implements UrlResolverInterface
{
    /**
     * @param string $webhookUrl
     * @param string $testEnvironmentWebhookUrl
     * @param bool $testMode
     *
     * @return string
     */
    public function resolveWebhookUrl(string $webhookUrl, string $testEnvironmentWebhookUrl, bool $testMode): string
    {
        if ($testMode) {
            return $testEnvironmentWebhookUrl;
        }

        return $webhookUrl;
    }
}
