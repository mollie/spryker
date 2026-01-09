<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Api\PaymentMethods\GetPaymentByTransactionIdApi;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
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
     * @return \Mollie\Client\Mollie\Api\PaymentMethods\GetPaymentByTransactionIdApi
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
     * @return \Mollie\Service\Mollie\MollieServiceInterface
     */
    public function getMollieService(): MollieServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::MOLLIE_SERVICE);
    }
}
