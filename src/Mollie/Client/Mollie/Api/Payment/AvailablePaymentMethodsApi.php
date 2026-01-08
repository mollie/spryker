<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class AvailablePaymentMethodsApi extends AbstractApiCall
{
    use LoggerTrait;

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieAvailablePaymentMethodsApiResponseTransfer = new MollieAvailablePaymentMethodsApiResponseTransfer();
        $mollieAvailablePaymentMethodsApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $mollieAvailablePaymentMethodCollectionTransfer = new MollieAvailablePaymentMethodCollectionTransfer();
        $methods = $mollieApiResponseTransfer->getPayload()['_embedded']['methods'] ?? [];
        foreach ($methods as $method) {
            $molliePaymentMethodTransfer = new MolliePaymentMethodTransfer();

            $molliePaymentMethodTransfer
                ->setId($method['id'])
                ->setDescription($method['description']);

            $mollieAvailablePaymentMethodCollectionTransfer->addMethods($molliePaymentMethodTransfer);
        }

        $mollieAvailablePaymentMethodsApiResponseTransfer->setCollection($mollieAvailablePaymentMethodCollectionTransfer);

        return $mollieAvailablePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        return new GetEnabledMethodsRequest(
            'oneOff',
            MethodQuery::RESOURCE_PAYMENTS,
        );
    }
}
