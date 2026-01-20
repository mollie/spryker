<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi;
use Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    protected const string TEST_MODE_IDENTIFIER = 'test';

    protected const string LIVE_MODE_IDENTIFIER = 'live';

    /**
     * @param \Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi $getEnabledPaymentMethodsApi
     * @param \Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi $getAllPaymentMethodsApi
     * @param \Mollie\Client\Mollie\MollieConfig $config
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $encodingService
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface $storageClient
     */
    public function __construct(
        protected GetEnabledPaymentMethodsApi $getEnabledPaymentMethodsApi,
        protected GetAllPaymentMethodsApi $getAllPaymentMethodsApi,
        protected MollieConfig $config,
        protected MollieToUtilEncodingServiceInterface $encodingService,
        protected MollieToStorageClientInterface $storageClient,
    ) {
    }

     /**
      * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
      *
      * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
      */
    public function getEnabledPaymentMethods(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
    ): MolliePaymentMethodsApiResponseTransfer {
        $molliePaymentMethodsApiResponseTransfer = $this->getCachedResponse($mollieApiRequestTransfer);
        if (!$molliePaymentMethodsApiResponseTransfer) {
            /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
            $molliePaymentMethodsApiResponseTransfer = $this->getEnabledPaymentMethodsApi->execute($mollieApiRequestTransfer);

            $this->cacheResponse($mollieApiRequestTransfer, $molliePaymentMethodsApiResponseTransfer);
        }

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer
     */
    public function getAllPaymentMethods(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
    ): MolliePaymentMethodsApiResponseTransfer {
        $molliePaymentMethodsApiResponseTransfer = $this->getCachedResponse($mollieApiRequestTransfer);
        if (!$molliePaymentMethodsApiResponseTransfer) {

            /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
            $molliePaymentMethodsApiResponseTransfer = $this->getAllPaymentMethodsApi->execute($mollieApiRequestTransfer);

            $this->cacheResponse($mollieApiRequestTransfer, $molliePaymentMethodsApiResponseTransfer);
        }

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer|null
     */
    protected function getCachedResponse(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
    ): ?MolliePaymentMethodsApiResponseTransfer {
        $cacheKey = $this->generateCacheKey($mollieApiRequestTransfer);
        $data = $this->storageClient->get($cacheKey);

        if (!$data) {
            return null;
        }

        $molliePaymentMethodsApiResponseTransfer = new MolliePaymentMethodsApiResponseTransfer();
        $molliePaymentMethodCollectionTransfer = new MolliePaymentMethodCollectionTransfer();

        $molliePaymentMethodCollectionTransfer->fromArray($data, true);

        $molliePaymentMethodsApiResponseTransfer
            ->setIsSuccessful(true)
            ->setCollection($molliePaymentMethodCollectionTransfer);

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return string
     */
    protected function generateCacheKey(MollieApiRequestTransfer $mollieApiRequestTransfer): string
    {
        $queryParametersTransfer = $mollieApiRequestTransfer->getMolliePaymentMethodQueryParameters();
        $mode = $this->config->getMollieTestModeEnabled() ? static::TEST_MODE_IDENTIFIER : static::LIVE_MODE_IDENTIFIER;

        $profileId = $queryParametersTransfer->getProfileId();
        $amount = $queryParametersTransfer->getAmount();
        $keyParts = [
            $queryParametersTransfer->getStoreName(),
            $profileId,
            $mode,
            $queryParametersTransfer->getLocale(),
            $amount?->getCurrency(),
            $amount?->getValue(),
            $queryParametersTransfer->getBillingCountry(),
            $queryParametersTransfer->getSequenceType(),
        ];

         return implode(':', array_filter($keyParts));
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer
     *
     * @return void
     */
    protected function cacheResponse(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
        MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer,
    ): void {
        $cacheKey = $this->generateCacheKey($mollieApiRequestTransfer);
        $ttl = $this->config->getMolliePaymentMethodsStorageKeyTTL();
        $data = $this->encodingService->encodeJson($molliePaymentMethodsApiResponseTransfer->getCollection()->toArray());

        $this->storageClient->set($cacheKey, $data, $ttl);
    }
}
