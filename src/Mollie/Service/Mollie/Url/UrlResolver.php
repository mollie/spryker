<?php

namespace Mollie\Service\Mollie\Url;

class UrlResolver implements UrlResolverInterface
{
    /**
     * @param string $username
     * @param string $password
     * @param string $webhookUrl
     *
     * @return string
     */
    public function resolveWebhookUrl(string $username, string $password, string $webhookUrl): string
    {
        if ($username && $password) {
            return sprintf('https://%s:%s@%s', $username, $password, $webhookUrl);
        }

        return 'https://' . $webhookUrl;
    }
}
