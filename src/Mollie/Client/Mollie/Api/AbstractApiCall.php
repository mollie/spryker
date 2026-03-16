<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api;

use Exception;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Response as MollieApiHttpResponse;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiCall implements ApiCallInterface
{
    public const string URL_FORMAT = '%s/%s';

    public const string MASKED = '***';

    protected Request|null $request = null;

    protected static ?string $correlationId = null;

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Client\Mollie\Logger\MollieLoggerInterface $logger
     */
    public function __construct(
        protected MollieApiClient $mollieApiClient,
        protected MollieConfig $mollieConfig,
        protected MollieToUtilEncodingServiceInterface $utilEncodingService,
        protected MollieLoggerInterface $logger,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    abstract protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    abstract protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function execute(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): AbstractTransfer
    {
        $mollieApiResponseTransfer = new MollieApiResponseTransfer();

        try {
            $this->mollieApiClient->setApiKey($this->mollieConfig->getMollieApiKey());
            $request = $this->buildRequest($mollieApiRequestTransfer);

            $result = $this->mollieApiClient->send($request);
            [$statusCode, $response] = $this->resolveResult($result);

            if ($statusCode === Response::HTTP_OK || $statusCode === Response::HTTP_CREATED) {
                $payload = $this->formatApiResponse($response);
                $mollieApiResponseTransfer = $this->createSuccessResponse($statusCode, $payload);
            }
        } catch (ApiException $exception) {
            $response = $exception->getResponse();
            $payload = $this->formatApiResponse($response->getPsrResponse());
            $errorCode = $response->status();
            $mollieApiResponseTransfer = $this->createErrorResponse($errorCode, $payload);
        } catch (Exception $exception) {
            $mollieApiResponseTransfer = $this->createExceptionResponse($exception->getMessage());
        }

        $this->logApi($mollieApiResponseTransfer);

        return $this->mapApiResponse($mollieApiResponseTransfer);
    }

    /**
     * @param mixed $result
     *
     * @return array<int, mixed>
     */
    protected function resolveResult(mixed $result): array
    {
        if ($result instanceof MollieApiHttpResponse) {
            return [
                $result->getPsrResponse()->getStatusCode(),
                $result->getPsrResponse(),
            ];
        }

        return [
            $result->getResponse()->status(),
            $result->getResponse()->getPsrResponse(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return void
     */
    protected function logApi(MollieApiResponseTransfer $mollieApiResponseTransfer): void
    {
        $apiLogTransfer = $this->mapApiResponseToLogResponseTransfer($mollieApiResponseTransfer);
        $this->logger->logResponse($apiLogTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieLogApiTransfer
     */
    protected function mapApiResponseToLogResponseTransfer(MollieApiResponseTransfer $mollieApiResponseTransfer): MollieLogApiTransfer
    {
        return (new MollieLogApiTransfer())
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setRequestIdentifier($this->getCorrelationId())
            ->setUrl($this->buildUrl())
            ->setRequestBody($this->getRequestBody())
            ->setPayload($mollieApiResponseTransfer->getPayload())
            ->setCode($mollieApiResponseTransfer->getCode())
            ->setMessage($mollieApiResponseTransfer->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestBody(): array
    {
        return $this->request?->query()->all() ?? [];
    }

    /**
     * @return string
     */
    protected function buildUrl(): string
    {
        return sprintf(
            static::URL_FORMAT,
            $this->mollieApiClient->resolveBaseUrl(),
            $this->request?->resolveResourcePath(),
        );
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     *
     * @return array<string, mixed>
     */
    protected function formatApiResponse(ResponseInterface $psrResponse): array
    {
        return $this->utilEncodingService->decodeJson($psrResponse->getBody()->getContents());
    }

    /**
     * @param int $statusCode
     * @param array<string, mixed> $payload
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function createSuccessResponse(int $statusCode, array $payload): MollieApiResponseTransfer
    {
        $mollieResponseApiResponseTransfer = new MollieApiResponseTransfer();
        $mollieResponseApiResponseTransfer
            ->setIsSuccessful(true)
            ->setCode($statusCode)
            ->setPayload($payload);

        return $mollieResponseApiResponseTransfer;
    }

    /**
     * @param int $errorCode
     * @param array<string, mixed> $payload
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function createErrorResponse(int $errorCode, array $payload): MollieApiResponseTransfer
    {
        $mollieResponseApiResponseTransfer = new MollieApiResponseTransfer();
        $mollieResponseApiResponseTransfer
            ->setIsSuccessful(false)
            ->setCode($errorCode)
            ->setMessage($payload['detail'] ?? '');

        return $mollieResponseApiResponseTransfer;
    }

     /**
      * @param string $message
      *
      * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
      */
    protected function createExceptionResponse(string $message): MollieApiResponseTransfer
    {
        $mollieResponseApiResponseTransfer = new MollieApiResponseTransfer();
        $mollieResponseApiResponseTransfer
            ->setIsSuccessful(false)
            ->setMessage($message);

        return $mollieResponseApiResponseTransfer;
    }

    /**
     * @return string
     */
    protected function getCorrelationId(): string
    {
        if (static::$correlationId === null) {
            static::$correlationId = Uuid::uuid4()->toString();
        }
        $reflection = new ReflectionClass($this);

        return sprintf('%s_%s', $reflection->getShortName(), static::$correlationId);
    }

     /**
      * @param array<int, string> $fieldsToMask
      * @param array<string, mixed> $payload
      *
      * @return array<string, mixed>
      */
    protected function maskPayload(array $fieldsToMask, array $payload): array
    {
        foreach ($fieldsToMask as $field) {
            $this->maskField($payload, $field);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @param string $path
     *
     * @return void
     */
    protected function maskField(array &$payload, string $path): void
    {
        $keys = explode('.', $path);
        $current = &$payload;

        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                return;
            }
            $current = &$current[$key];
        }

        $current = static::MASKED;
    }
}
