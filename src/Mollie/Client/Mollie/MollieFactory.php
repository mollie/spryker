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
use Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProvider;
use Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProviderInterface;
use Mollie\Client\Mollie\Mapper\PaymentMethodMapper;
use Mollie\Client\Mollie\Mapper\PaymentMethodMapperInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Client\Kernel\AbstractFactory;

/**
 * @method \Mollie\Client\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Client\Mollie\Provider\Payment\PaymentMethodsProviderInterface
     */
    public function createAvailablePaymentMethodsProvider(): PaymentMethodsProviderInterface
    {
        return new PaymentMethodsProvider(
            $this->createAvailablePaymentMethodsApi(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
            $this->getStorageClient(),
            $this->getStoreClient(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi
     */
    public function createGetEnabledPaymentMethodsApi(): ApiCallInterface
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
    public function createGetAllPaymentMethodsApi(): ApiCallInterface
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
     * @return \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface
     */
    public function getStoreClient(): MollieToStoreClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORE);
    }
}
