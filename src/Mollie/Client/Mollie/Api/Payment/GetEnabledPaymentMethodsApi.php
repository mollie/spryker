<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\MethodQuery;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMethodsMapperInterface;
use Mollie\Client\Mollie\MollieConfig;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;

class GetEnabledPaymentMethodsApi extends AbstractApiCall
{
    use LoggerTrait;

    protected const string METHODS_WRAPPER_KEY = '_embedded';

    protected const string METHODS_KEY = 'methods';

    /**
     * @param \Mollie\Api\MollieApiClient $mollieApiClient
     * @param \Mollie\Client\Mollie\MollieConfig $mollieConfig
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Client\Mollie\Mapper\Payment\PaymentMethodsMapperInterface $mapper
     */
    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieConfig $mollieConfig,
        MollieToUtilEncodingServiceInterface $utilEncodingService,
        protected PaymentMethodsMapperInterface $mapper,
    ) {
        parent::__construct($mollieApiClient, $mollieConfig, $utilEncodingService);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $molliePaymentMethodsApiResponseTransfer = new MolliePaymentMethodsApiResponseTransfer();
        $molliePaymentMethodsApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentMethodCollectionTransfer = $this->mapper->mapPayloadToMolliePaymentMethodCollectionTransfer($mollieApiResponseTransfer->getPayload());
        $molliePaymentMethodsApiResponseTransfer->setCollection($molliePaymentMethodCollectionTransfer);

        return $molliePaymentMethodsApiResponseTransfer;
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
            $queryParametersTransfer->getIncludeWallets(),
            $queryParametersTransfer->getOrderLineCategories(),
            $queryParametersTransfer->getProfileId(),
            $queryParametersTransfer->getIncludeIssuers(),
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
            $amountTransfer->getCurrency(),
            $amountTransfer->getValue(),
        );
    }
}
