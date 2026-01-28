<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Log\LoggerTrait;

class MollieLogger implements MollieLoggerInterface
{
    use LoggerTrait;

    private string $mode;

    public function __construct(protected MollieConfig $config)
    {
        $this->mode = $this->config->getMollieLoggingMode();
    }

    /**
     * @param string $apiName
     * @param array $requestData
     * @param MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    public function logResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        if ($this->mode === 'off') {
            return;
        }

        if ($responseTransfer->getIsSuccessful()) {
            $this->logSuccessfulResponse($apiName, $url, $requestData, $responseTransfer);

            return;
        }

        $this->logFailedResponse($apiName, $url, $requestData, $responseTransfer);
    }

    /**
     * @return void
     */
    protected function logSuccessfulResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        $message = "API call {$apiName} successful";
        $context = [
            'url' => $url,
            'requestBody' => $requestData,
            'statusCode' => $responseTransfer->getCode(),
        ];

        if ($this->mode === 'Extensive') {
            $context['requestBody'] = $requestData;
            $context['responseBody'] = $responseTransfer->getPayload();
        }

        $this->getLogger()->info($message, $context);
    }

    /**
     * @return void
     */
    protected function logFailedResponse(string $apiName, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        $message = "API call {$apiName} failed";
        $context = [
            'url' => $url,
            'statusCode' => $responseTransfer->getCode(),
            'errorMessage' => $responseTransfer->getMessage(),
        ];

        if ($this->mode === 'Extensive') {
            $context['requestBody'] = $requestData;
        }

        $this->getLogger()->error($message, $context);
    }
}
