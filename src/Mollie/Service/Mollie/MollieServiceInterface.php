<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie;

interface MollieServiceInterface
{
    /**
     * Calls IntegerToDecimalConverter class from shared layer
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal(int $value): float;

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
