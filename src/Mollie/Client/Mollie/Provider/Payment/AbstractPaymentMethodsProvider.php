<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodsApiResponseTransfer;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToLocaleClientInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMethodsMapperInterface;
use Mollie\Client\Mollie\MollieConfig;

abstract class AbstractPaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    protected const string DELIMITER = ':';
    /**
     * @param \Mollie\Client\Mollie\Api\ApiCallInterface $enabledPaymentMethodsApi
     * @param \Mollie\Client\Mollie\MollieConfig $config
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $encodingService
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface $storeClient
     */
    public function __construct(
        protected ApiCallInterface $paymentMethodsApi,
        protected PaymentMethodsMapperInterface $mapper,
        protected MollieConfig $config,
        protected MollieToUtilEncodingServiceInterface $encodingService,
        protected MollieToStorageClientInterface $storageClient,
        protected MollieToStoreClientInterface $storeClient,
        protected MollieToLocaleClientInterface $localeClient,
    ) {
    }

    /**
     * @param MollieApiRequestTransfer $requestTransfer
     *
     * @return string
     */
    abstract protected function generateCacheKey(MollieApiRequestTransfer $requestTransfer): string;

    /**
     * @param MollieApiRequestTransfer $requestTransfer
     *
     * @return MolliePaymentMethodsApiResponseTransfer
     */
    public function provide(MollieApiRequestTransfer $mollieApiRequestTransfer): MolliePaymentMethodsApiResponseTransfer
    {
        $key = $this->generateCacheKey($mollieApiRequestTransfer);
        $cachedResponse = $this->getCachedResponse($key);
        if ($cachedResponse)  {
            return $cachedResponse;
        }

        $responseTransfer = $this->PaymentMethodsApi->execute($mollieApiRequestTransfer);
        if ($responseTransfer->isSuccessful()) {
            $this->cacheResponse($key, $responseTransfer);
        }

        return $responseTransfer;
    }

    /**
     * @param string $key
     *
     * @return MolliePaymentMethodsApiResponseTransfer|null
     */
    protected function getCachedResponse(string $key): MolliePaymentMethodsApiResponseTransfer|null
    {
        $cached = $this->storageClient->get($key);
        if ($cached === null) {
            return null;
        }

        return $this->mapper->mapPayloadToMolliePaymentMethodCollectionTransfer($cached);
    }

    /**
     * @param string $key
     * @param MolliePaymentMethodsApiResponseTransfer $transfer
     *
     * @return void
     */
    protected function cacheResponse(string $key, MolliePaymentMethodsApiResponseTransfer $transfer): void
    {
        $ttl = $this->config->getMolliePaymentMethodsStorageKeyTTL();
        $methods = $transfer->getCollection()->getMethods()->getArrayCopy();
        $payload = $this->encodingService->encodeJson($methods);
        $this->storageClient->set($key, $payload, $ttl);
    }
}
