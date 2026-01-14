<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class AvailablePaymentMethodsApi extends AbstractApiCall
{
    use LoggerTrait;

    protected const string METHODS_WRAPPER_KEY = '_embedded';

    protected const string METHODS_KEY = 'methods';

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieAvailablePaymentMethodsApiResponseTransfer = new MollieAvailablePaymentMethodsApiResponseTransfer();
        $mollieAvailablePaymentMethodsApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $mollieAvailablePaymentMethodCollectionTransfer = new MollieAvailablePaymentMethodCollectionTransfer();
        $methods = $mollieApiResponseTransfer->getPayload()[static::METHODS_WRAPPER_KEY][static::METHODS_KEY] ?? [];
        foreach ($methods as $method) {
            $molliePaymentMethodTransfer = new MolliePaymentMethodTransfer();

            $molliePaymentMethodTransfer->fromArray($method, true);

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
        if (!$mollieApiRequestTransfer) {
            return new GetEnabledMethodsRequest(
                'oneoff',
                MethodQuery::RESOURCE_PAYMENTS,
            );
        }

        $queryParametersTransfer = $mollieApiRequestTransfer->getMolliePaymentMethodQueryParameters();
        $amount = $this->getAmount($queryParametersTransfer);

        return new GetEnabledMethodsRequest(
            $queryParametersTransfer->getSequenceType(),
            MethodQuery::RESOURCE_PAYMENTS,
            $queryParametersTransfer->getLocale(),
            $amount,
            $queryParametersTransfer->getBillingCountry(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $transfer
     *
     * @return \Mollie\Api\Http\Data\Money|null
     */
    protected function getAmount(MolliePaymentMethodQueryParametersTransfer $transfer): Money|null
    {
        $amountTransfer = $transfer->getAmount();
        if (!$amountTransfer) {
            return null;
        }

        return new Money(
            $amountTransfer->getValue(),
            $amountTransfer->getCurrency(),
        );
    }
}
