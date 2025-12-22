<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Api\Payment\GetPaymentApi;
use Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMapper;
use Mollie\Client\Mollie\Storage\MolliePaymentStorageSaver;
use Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface;
use Spryker\Client\Kernel\AbstractFactory;

/**
 * @method \Mollie\Client\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi
     */
    public function createAvailablePaymentMethodsApi(): ApiCallInterface
    {
        return new AvailablePaymentMethodsApi(
            $this->createMollieApiClient(),
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
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createCreatePaymentApi(): ApiCallInterface
    {
        return new CreatePaymentApi(
            $this->createMollieApiClient(),
            $this->createPaymentMapper(),
            $this->createMolliePaymentStorageSaver(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createGetPaymentApi(): ApiCallInterface
    {
        return new GetPaymentApi(
            $this->createMollieApiClient(),
            $this->createPaymentMapper(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface
     */
    protected function createPaymentMapper(): MollieApiResponseMapperInterface
    {
        return new PaymentMapper();
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\MollieToStorageClientInterface
     */
    protected function createStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface
     */
    protected function createMolliePaymentStorageSaver(): MolliePaymentStorageSaverInterface
    {
        return new MolliePaymentStorageSaver(
            $this->createStorageClient(),
        );
    }
}
