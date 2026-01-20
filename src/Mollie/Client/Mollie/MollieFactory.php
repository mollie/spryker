<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetPaymentByTransactionIdApi;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
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
            $this->createPaymentMethodMapper(),
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
    public function createPaymentApi(): ApiCallInterface
    {
        return new CreatePaymentApi(
            $this->createMollieApiClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->getMollieService(),
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
