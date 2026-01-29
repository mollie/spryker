<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Log\LoggerTrait;

class MollieLogger implements MollieLoggerInterface
{
    use LoggerTrait;

    private string $mode;

    protected const string SUCCESS_MESSAGE = 'API call %s successful';

    protected const string ERROR_MESSAGE = 'API call %s failed';

    /**
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
        $this->mode = $this->config->getMollieLoggingMode();
    }

    /**
     * @param string $apiName
     * @param string $url
     * @param array<string, mixed> $requestData
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    public function logResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        if ($this->mode === MollieConstants::MOLLIE_LOGGER_OFF) {
            return;
        }

        if ($responseTransfer->getIsSuccessful()) {
            $this->logSuccessfulResponse($apiName, $url, $requestData, $responseTransfer);

            return;
        }

        $this->logFailedResponse($apiName, $url, $requestData, $responseTransfer);
    }

    /**
     * @param string $apiName
     * @param string $url
     * @param array<string, mixed> $requestData
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function logSuccessfulResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        $message = sprintf(static::SUCCESS_MESSAGE, $apiName);
        $context = [
            'url' => $url,
            'requestBody' => $requestData,
            'statusCode' => $responseTransfer->getCode(),
        ];

        if ($this->mode === MollieConstants::MOLLIE_LOGGER_EXTENSIVE) {
            $context['requestBody'] = $requestData;
            $context['responseBody'] = $responseTransfer->getPayload();
        }

        $this->getLogger()->info($message, $context);
    }

    /**
     * @param string $apiName
     * @param string $url
     * @param array<string, mixed> $requestData
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function logFailedResponse(string $apiName, string $url, array $requestData, MollieApiResponseTransfer $responseTransfer): void
    {
        $message = sprintf(static::ERROR_MESSAGE, $apiName);
        $context = [
            'url' => $url,
            'statusCode' => $responseTransfer->getCode(),
            'errorMessage' => $responseTransfer->getMessage(),
        ];

        if ($this->mode === MollieConstants::MOLLIE_LOGGER_EXTENSIVE) {
            $context['requestBody'] = $requestData;
        }

        $this->getLogger()->error($message, $context);
    }
}
