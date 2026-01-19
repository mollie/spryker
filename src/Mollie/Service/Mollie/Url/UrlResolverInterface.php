<?php

namespace Mollie\Service\Mollie\Url;

interface UrlResolverInterface
{
    /**
     * Calls UrlResolver class from client layer
     *
     * @api
     *
     * @param string $username
     * @param string $password
     * @param string $webhookUrl
     *
     * @return string
     */
    public function resolveWebhookUrl(string $username, string $password, string $webhookUrl): string;
}
