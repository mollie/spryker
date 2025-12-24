<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Api\PaymentMethods\GetPaymentById;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
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
            $this->getConfig(),
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Api\PaymentMethods\GetPaymentById
     */
    public function createGetPaymentByIdApi(): ApiCallInterface
    {
        return new GetPaymentById(
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
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
