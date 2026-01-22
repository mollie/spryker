<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Generator\Payment\PaymentMethodsCacheKeyGeneratorInterface;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsCacheDeleter implements PaymentMethodsCacheDeleterInterface
{
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
        $this->storageClient->delete($cacheKey);
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
        $this->storageClient->delete($cacheKey);
    }
}
