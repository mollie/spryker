<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentMethods;

use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class AvailablePaymentMethodsApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param array<string, mixed> $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function getApiResponse(array $query): MollieApiResponseTransfer
    {
        try {
            $methods = $this->mollieApiClient->methods->allEnabled($query, true);

            $mollieApiResponseTransfer = new MollieApiResponseTransfer();
            $mollieApiResponseTransfer
                ->setIsSuccessful($methods->count() > 0)
                ->setPayload($methods->getArrayCopy());

            return $mollieApiResponseTransfer;
        } catch (ApiException $requestException) {
            throw new ApiException(
                $requestException->getResponse(),
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function formatApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieAvailablePaymentMethodCollectionTransfer = new MollieAvailablePaymentMethodCollectionTransfer();
        foreach ($mollieApiResponseTransfer->getPayload() as $method) {
            $molliePaymentMethodTransfer = new MolliePaymentMethodTransfer();

            $molliePaymentMethodTransfer
                ->setId($method->id)
                ->setDescription($method->description)
                ->setMinimumAmount($method->minimumAmount->value);

            $mollieAvailablePaymentMethodCollectionTransfer->addMethods($molliePaymentMethodTransfer);
        }

        return $mollieAvailablePaymentMethodCollectionTransfer;
    }
}
