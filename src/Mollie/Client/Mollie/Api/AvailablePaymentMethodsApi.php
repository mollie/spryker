<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Client\Mollie\Mapper\ApiResponseMapperInterface;
use Spryker\Shared\Log\LoggerTrait;

class AvailablePaymentMethodsApi
{
    use LoggerTrait;

    /**
     * @param \Mollie\Client\Mollie\Mapper\ApiResponseMapperInterface $mapper
     */
    public function __construct(protected ApiResponseMapperInterface $mapper)
    {
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
     */
    public function getAvailablePaymentMethods(): MollieAvailablePaymentMethodCollectionTransfer
    {
        $mollieApiClient = new MollieApiClient();

        try {
            $methods = $mollieApiClient->methods->allEnabled();

            $mollieAvailablePaymentMethodCollectionTransfer = $this->mapper->mapMolliePaymentMethodsToMolliePaymentMethodTransfer($methods);
        } catch (ApiException $requestException) {
            throw new ApiException(
                $requestException->getResponse(),
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }

        return $mollieAvailablePaymentMethodCollectionTransfer;
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodTransfer
     */
    public function getIdealPaymentMethod(): MolliePaymentMethodTransfer
    {
        $mollieApiClient = new MollieApiClient();

        try {
            $method = $mollieApiClient->methods->get(PaymentMethod::IDEAL);

            $molliePaymentMethodTransfer = $this->mapper->mapMolliePaymentMethodToMolliePaymentMethodTransfer($method);
        } catch (ApiException $requestException) {
            throw new ApiException(
                $requestException->getResponse(),
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }

        return $molliePaymentMethodTransfer;
    }
}
