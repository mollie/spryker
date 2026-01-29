<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieApiResponseTransfer;

interface MollieLoggerInterface
{
    /**
     * @param string $apiName
     * @param string $url
     * @param array<string, mixed> $requestData
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    public function logResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void;
}
