<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsCacheDeleter implements PaymentMethodsCacheDeleterInterface
{
    public const string KV_PREFIX = 'kv:';

    public const string WILDCARD = '*';

    public const int STORAGE_SCAN_LIMIT = 100;

    /**
     * @param \Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface $keyGenerator
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Client\Mollie\MollieConfig $config
     */
    public function __construct(
        protected PaymentMethodsCacheKeyGeneratorInterface $keyGenerator,
        protected MollieToStorageClientInterface $storageClient,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return void
     */
    public function deleteEnabledPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void
    {
        $cacheKeyPrefix = $this->config->getCacheKeyPrefixForEnabledPaymentMethods();
        $cacheKey = $this->keyGenerator->generateCacheKey($parameters, $cacheKeyPrefix);
        $cacheKeyWithWildcard = $cacheKey . static::WILDCARD;
        $storageTransfer = $this->storageClient->scanKeys($cacheKeyWithWildcard, static::STORAGE_SCAN_LIMIT);
        $formattedKeys = $this->formatKeys($storageTransfer->getKeys());
        $this->storageClient->deleteMulti($formattedKeys);
    }

     /**
      * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
      *
      * @return void
      */
    public function deleteAllPaymentMethodsCache(MolliePaymentMethodQueryParametersTransfer $parameters): void
    {
        $cacheKeyPrefix = $this->config->getCacheKeyPrefixForAllPaymentMethods();
        $cacheKey = $this->keyGenerator->generateCacheKey($parameters, $cacheKeyPrefix);
        $storageTransfer = $this->storageClient->scanKeys($cacheKey, static::STORAGE_SCAN_LIMIT);
        $formattedKeys = $this->formatKeys($storageTransfer->getKeys());
        $this->storageClient->deleteMulti($formattedKeys);
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string>
     */
    protected function formatKeys(array $keys): array
    {
        $formattedKeys = [];
        foreach ($keys as $key) {
            $formattedKeys[] = $this->truncateKeyPrefix($key);
        }

        return $formattedKeys;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function truncateKeyPrefix(string $key): string
    {
        if (str_starts_with($key, static::KV_PREFIX)) {
            return substr($key, strlen(static::KV_PREFIX));
        }

        return $key;
    }
}
