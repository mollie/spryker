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
use Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    /**
     * @param \Mollie\Client\Mollie\Api\Payment\GetEnabledPaymentMethodsApi $getEnabledPaymentMethodsApi
     * @param \Mollie\Client\Mollie\Api\Payment\GetAllPaymentMethodsApi $getAllPaymentMethodsApi
     * @param \Mollie\Client\Mollie\MollieConfig $config
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $encodingService
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface $cacheKeyGenerator
     */
    public function __construct(
        protected GetEnabledPaymentMethodsApi $getEnabledPaymentMethodsApi,
        protected GetAllPaymentMethodsApi $getAllPaymentMethodsApi,
        protected MollieConfig $config,
        protected MollieToUtilEncodingServiceInterface $encodingService,
        protected MollieToStorageClientInterface $storageClient,
        protected PaymentMethodsCacheKeyGeneratorInterface $cacheKeyGenerator,
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
        $cacheKeyPrefix = $this->config->getCacheKeyPrefixForEnabledPaymentMethods();
        $molliePaymentMethodsApiResponseTransfer = $this->getCachedResponse($mollieApiRequestTransfer, $cacheKeyPrefix);
        if (!$molliePaymentMethodsApiResponseTransfer) {
            /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
            $molliePaymentMethodsApiResponseTransfer = $this->getEnabledPaymentMethodsApi->execute($mollieApiRequestTransfer);

            if ($molliePaymentMethodsApiResponseTransfer->getIsSuccessful()) {
                 $this->cacheResponse(
                     $mollieApiRequestTransfer,
                     $molliePaymentMethodsApiResponseTransfer,
                     $cacheKeyPrefix,
                 );
            }
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
        $cacheKeyPrefix = $this->config->getCacheKeyPrefixForAllPaymentMethods();
        $molliePaymentMethodsApiResponseTransfer = $this->getCachedResponse($mollieApiRequestTransfer, $cacheKeyPrefix);
        if (!$molliePaymentMethodsApiResponseTransfer) {

            /** @var \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer */
            $molliePaymentMethodsApiResponseTransfer = $this->getAllPaymentMethodsApi->execute($mollieApiRequestTransfer);

            if ($molliePaymentMethodsApiResponseTransfer->getIsSuccessful()) {
                $this->cacheResponse(
                    $mollieApiRequestTransfer,
                    $molliePaymentMethodsApiResponseTransfer,
                    $cacheKeyPrefix,
                );
            }
        }

        return $molliePaymentMethodsApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     * @param string $cacheKeyPrefix
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer|null
     */
    protected function getCachedResponse(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
        string $cacheKeyPrefix,
    ): ?MolliePaymentMethodsApiResponseTransfer {
        $queryParametersTransfer = $mollieApiRequestTransfer->getMolliePaymentMethodQueryParameters();
        $cacheKey = $this->cacheKeyGenerator->generateCacheKey($queryParametersTransfer, $cacheKeyPrefix);
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
     * @param \Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer
     * @param string $cacheKeyPrefix
     *
     * @return void
     */
    protected function cacheResponse(
        MollieApiRequestTransfer $mollieApiRequestTransfer,
        MolliePaymentMethodsApiResponseTransfer $molliePaymentMethodsApiResponseTransfer,
        string $cacheKeyPrefix,
    ): void {
        $queryParametersTransfer = $mollieApiRequestTransfer->getMolliePaymentMethodQueryParameters();
        $cacheKey = $this->cacheKeyGenerator->generateCacheKey($queryParametersTransfer, $cacheKeyPrefix);
        $ttl = $this->config->getMolliePaymentMethodsStorageKeyTTL();
        $data = $this->encodingService->encodeJson($molliePaymentMethodsApiResponseTransfer->getCollection()->toArray());

        $this->storageClient->set($cacheKey, $data, $ttl);
    }
}
