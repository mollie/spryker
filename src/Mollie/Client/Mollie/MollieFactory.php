<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Capture\CreatePaymentCaptureApi;
use Mollie\Client\Mollie\Api\Capture\GetPaymentCaptureApi;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetPaymentByTransactionIdApi;
use Mollie\Client\Mollie\Api\Payment\ReleasePaymentAuthorizationApi;
use Mollie\Client\Mollie\Api\Profile\GetCurrentProfileApi;
use Mollie\Client\Mollie\Api\Refund\CreateRefundApi;
use Mollie\Client\Mollie\Api\Refund\GetRefundByRefundIdApi;
use Mollie\Client\Mollie\Deleter\Payment\PaymentMethodsCacheDeleter;
use Mollie\Client\Mollie\Deleter\Payment\PaymentMethodsCacheDeleterInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGenerator;
use Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface;
use Mollie\Client\Mollie\Logger\MollieLogger;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\Mapper\PaymentMethodMapper;
use Mollie\Client\Mollie\Mapper\PaymentMethodMapperInterface;
use Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProvider;
use Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProviderInterface;
use Mollie\Client\Mollie\Zed\MollieStub;
use Mollie\Client\Mollie\Zed\MollieStubInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

/**
 * @method \Mollie\Client\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProviderInterface
     */
    public function createPaymentMethodsProvider(): PaymentMethodsProviderInterface
    {
        return new PaymentMethodsProvider(
            $this->createGetEnabledPaymentMethodsApi(),
            $this->createGetAllPaymentMethodsApi(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->getStorageClient(),
            $this->createPaymentMethodsCacheKeyGenerator(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface
     */
    public function createPaymentMethodsCacheKeyGenerator(): PaymentMethodsCacheKeyGeneratorInterface
    {
        return new PaymentMethodsCacheKeyGenerator(
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Deleter\Payment\PaymentMethodsCacheDeleterInterface
     */
    public function createPaymentMethodsCacheDeleter(): PaymentMethodsCacheDeleterInterface
    {
        return new PaymentMethodsCacheDeleter(
            $this->createPaymentMethodsCacheKeyGenerator(),
            $this->getStorageClient(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi
     */
    public function createGetEnabledPaymentMethodsApi(): GetEnabledPaymentMethodsApi
    {
        return new GetEnabledPaymentMethodsApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
            $this->createPaymentMethodMapper(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi
     */
    public function createGetAllPaymentMethodsApi(): GetAllPaymentMethodsApi
    {
        return new GetAllPaymentMethodsApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
            $this->createPaymentMethodMapper(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\Profile\GetCurrentProfileApi
     */
    public function createGetCurrentProfileApi(): GetCurrentProfileApi
    {
        return new GetCurrentProfileApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\Payment\GetPaymentByTransactionIdApi
     */
    public function createGetPaymentByTransactionIdApi(): ApiCallInterface
    {
        return new GetPaymentByTransactionIdApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createGetPaymentCaptureApi(): ApiCallInterface
    {
        return new GetPaymentCaptureApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createPaymentApi(): ApiCallInterface
    {
        return new CreatePaymentApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
            $this->getMollieService(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createReleasePaymentAuthorizationApi(): ApiCallInterface
    {
        return new ReleasePaymentAuthorizationApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createPaymentCaptureApi(): ApiCallInterface
    {
        return new CreatePaymentCaptureApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Api\MollieApiClient
     */
    public function createMollieApiClient(): MollieApiClient
    {
        return new MollieApiClient();
    }

    /**
     * @return \Mollie\Client\Mollie\Zed\MollieStubInterface
     */
    public function createZedMollieStub(): MollieStubInterface
    {
        return new MollieStub(
            $this->getZedRequestClient(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::UTIL_ENCODING_SERVICE);
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createRefundApi(): ApiCallInterface
    {
        return new CreateRefundApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
            $this->getMollieService(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createGetRefundByRefundIdApi(): ApiCallInterface
    {
        return new GetRefundByRefundIdApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->createMollieLogger(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Mapper\PaymentMethodMapperInterface
     */
    public function createPaymentMethodMapper(): PaymentMethodMapperInterface
    {
        return new PaymentMethodMapper();
    }

    /**
     * @return \Mollie\Client\Mollie\Logger\MollieLoggerInterface
     */
    public function createMollieLogger(): MollieLoggerInterface
    {
        return new MollieLogger(
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Service\Mollie\MollieServiceInterface
     */
    public function getMollieService(): MollieServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::MOLLIE_SERVICE);
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    public function getZedRequestClient(): ZedRequestClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_ZED);
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }
}
