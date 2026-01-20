<?php

namespace Mollie\Service\Mollie\Url;

class UrlResolver implements UrlResolverInterface
{
    /**
     * @param string $webhookUrl
     * @param string $testEnvironmentWebhookUrl
     * @param string $testMode
     *
     * @return string
     */
    public function resolveWebhookUrl(string $webhookUrl, string $testEnvironmentWebhookUrl, string $testMode): string
    {
        if ($testMode) {
            return $testEnvironmentWebhookUrl;
        }

        return $webhookUrl;
    }
}
