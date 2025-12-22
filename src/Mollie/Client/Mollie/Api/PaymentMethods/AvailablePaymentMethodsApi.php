<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\PaymentMethods;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Api\Exception\AvailablePaymentMethodsApiException;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class AvailablePaymentMethodsApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Mollie\Api\Http\Request $request
     *
     * @throws \Mollie\Client\Mollie\Api\Exception\AvailablePaymentMethodsApiException
     *
     * @return \Generated\Shared\Transfer\MollieApiResponseTransfer
     */
    protected function send(Request $request): MollieApiResponseTransfer
    {
        try {
            $methods = $this->mollieApiClient->send($request);

            $mollieApiResponseTransfer = new MollieApiResponseTransfer();
            $mollieApiResponseTransfer
                ->setIsSuccessful($methods->count() > 0)
                ->setPayload($methods->getArrayCopy());

            return $mollieApiResponseTransfer;
        } catch (ApiException | MollieException $requestException) {
            $logException = sprintf(
                'Error calling available api payment methods with message: %s',
                $requestException->getMessage(),
            );

            $this->getLogger()->error($logException);

            throw new AvailablePaymentMethodsApiException(
                $requestException->getMessage(),
                $requestException->getCode(),
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
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

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        return new GetEnabledMethodsRequest(
            resource: MethodQuery::RESOURCE_PAYMENTS,
        );
    }
}
