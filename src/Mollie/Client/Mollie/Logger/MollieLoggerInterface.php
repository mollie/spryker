<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieApiResponseTransfer;

interface MollieLoggerInterface
{
    /**
     * @param string $apiName
     * @param array $requestData
     * @param MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    public function logResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void;
}