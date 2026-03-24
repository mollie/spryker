<?php

declare(strict_types = 1);

namespace Mollie\Service\Mollie;

use Generated\Shared\Transfer\MollieAmountTransfer;

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
     * Converts Spryker integer amount into Mollie amount format
     *
     * @api
     *
     * @param int $value
     * @param string|null $currency
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    public function convertIntegerToMollieAmount(int $value, ?string $currency = null): MollieAmountTransfer;

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
