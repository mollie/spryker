<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Client\Mollie\Api\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\Mapper\ApiResponseMapper;
use Mollie\Client\Mollie\Mapper\ApiResponseMapperInterface;
use Spryker\Client\Kernel\AbstractFactory;

class MollieFactory extends AbstractFactory
{
    /**
     * @return \Mollie\Client\Mollie\Api\AvailablePaymentMethodsApi
     */
    public function createAvailablePaymentMethodsApi(): AvailablePaymentMethodsApi
    {
        return new AvailablePaymentMethodsApi(
            $this->createApiResponseMapper(),
        );
    }

    /**
     * @return \Mollie\Client\Mollie\Mapper\ApiResponseMapperInterface
     */
    public function createApiResponseMapper(): ApiResponseMapperInterface
    {
        return new ApiResponseMapper();
    }
}
