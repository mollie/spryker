<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api;

use Exception;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Response as MollieApiHttpResponse;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieConfig;
use ReflectionClass;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiCall implements ApiCallInterface
{
    public const string MASKED = '***';

    protected Request|null $request = null;

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
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return void
     */
    abstract protected function maskResponseData(MollieApiResponseTransfer $mollieApiResponseTransfer): MollieApiResponseTransfer;

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
            $response = $result->getResponse();

            if ($response->status() === Response::HTTP_OK || $response->status() === Response::HTTP_CREATED) {
                $payload = $this->formatApiResponse($response);
                $mollieApiResponseTransfer = $this->createSuccessResponse($payload);
            }
        } catch (ApiException $exception) {
            $response = $exception->getResponse();
            $payload = $this->formatApiResponse($response);
            $errorCode = $response->status();
            $mollieApiResponseTransfer = $this->createErrorResponse($errorCode, $payload);
        } catch (Exception $exception) {
            $mollieApiResponseTransfer = $this->createExceptionResponse($exception->getMessage());
        }

        $this->logResponse($mollieApiResponseTransfer);

        return $this->mapApiResponse($mollieApiResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return void
     */
    protected function logResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): void
    {
        $url = '';
        $requestData = [];
        if ($this->request) {
            $requestData = $this->request->query()->all();
            $url = $this->mollieApiClient->resolveBaseUrl() . '/' . $this->request->resolveResourcePath();
        }

        $maskedApiResponseTransfer = $this->maskResponseData($mollieApiResponseTransfer);
        $apiClassName = (new ReflectionClass($this))->getShortName();
        $this->logger->logResponse($apiClassName, $url, $requestData, $maskedApiResponseTransfer);
    }

    /**
     * @param \Mollie\Api\Http\Response $response
     *
     * @return array<string, string>
     */
    protected function formatApiResponse(MollieApiHttpResponse $response): array
    {
        $psrResponse = $response->getPsrResponse();

        return $this->utilEncodingService->decodeJson($psrResponse->getBody()->getContents());
    }

    /**
     * @param array<string, string> $payload
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function createSuccessResponse(array $payload): MollieApiResponseTransfer
    {
        $mollieResponseApiResponseTransfer = new MollieApiResponseTransfer();
        $mollieResponseApiResponseTransfer
            ->setIsSuccessful(true)
            ->setCode(Response::HTTP_OK)
            ->setPayload($payload);

        return $mollieResponseApiResponseTransfer;
    }

    /**
     * @param int $errorCode
     * @param array<string, string> $payload
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
}
