<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Api\Payment\GetPaymentApi;
use Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Dependency\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMapper;
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
            $this->getUtilEncodingService(),
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
        return new PaymentMapper(
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\MollieToUtilEncodingServiceInterface
     */
    protected function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
