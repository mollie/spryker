<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieLogApiTransfer;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Log\LoggerTrait;

class MollieLogger implements MollieLoggerInterface
{
    use LoggerTrait;

    private string $mode;

    protected const string API_SUCCESS_MESSAGE = 'API call %s successful';

    protected const string API_ERROR_MESSAGE = 'API call %s failed';

    /**
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
        $this->mode = $this->config->getMollieLoggingMode();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    public function logMessage(MollieLogApiTransfer $logApiTransfer): void
    {
        if ($this->mode === MollieConstants::MOLLIE_LOGGER_OFF) {
            return;
        }

        if ($logApiTransfer->getIsSuccessful()) {
            $this->logSuccessMessage($logApiTransfer);

            return;
        }

        $this->logErrorMessage($logApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    protected function logSuccessMessage(MollieLogApiTransfer $logApiTransfer): void
    {
        $this->getLogger()->info(
            $logApiTransfer->getMessage(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    protected function logErrorMessage(MollieLogApiTransfer $logApiTransfer): void
    {
        $this->getLogger()->error(
            $logApiTransfer->getMessage(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    public function logResponse(MollieLogApiTransfer $logApiTransfer): void
    {
        if ($this->mode === MollieConstants::MOLLIE_LOGGER_OFF) {
            return;
        }

        if ($logApiTransfer->getIsSuccessful()) {
            $this->logSuccessfulResponse($logApiTransfer);

            return;
        }

        $this->logFailedResponse($logApiTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    protected function logSuccessfulResponse(MollieLogApiTransfer $logApiTransfer): void
    {
        $message = sprintf(static::API_SUCCESS_MESSAGE, $logApiTransfer->getRequestIdentifier());
        $context = [
            'requestIdentifier' => $logApiTransfer->getRequestIdentifier(),
            'url' => $logApiTransfer->getUrl(),
            'statusCode' => $logApiTransfer->getCode(),
        ];

        if ($this->mode === MollieConstants::MOLLIE_LOGGER_EXTENSIVE) {
            $context['requestBody'] = $logApiTransfer->getRequestBody();
            $context['responseBody'] = $logApiTransfer->getPayload();
        }

        $this->getLogger()->info($message, $context);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    protected function logFailedResponse(MollieLogApiTransfer $logApiTransfer): void
    {
        $message = sprintf(static::API_ERROR_MESSAGE, $logApiTransfer->getRequestIdentifier());
        $context = [
            'requestIdentifier' => $logApiTransfer->getRequestIdentifier(),
            'url' => $logApiTransfer->getUrl(),
            'statusCode' => $logApiTransfer->getCode(),
            'errorMessage' => $logApiTransfer->getMessage(),
        ];

        if ($this->mode === MollieConstants::MOLLIE_LOGGER_EXTENSIVE) {
            $context['requestBody'] = $logApiTransfer->getRequestBody();
        }

        $this->getLogger()->error($message, $context);
    }
}
