<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetPaymentByTransactionIdApi;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Spryker\Client\Kernel\AbstractFactory;

/**
 * @method \Mollie\Client\Mollie\MollieConfig getConfig()
 */
class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Client\Mollie\Api\Payment\AvailablePaymentMethodsApi
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
}
