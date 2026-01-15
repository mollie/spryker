<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Provider\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Client\Mollie\MollieConfig;

class PaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    /**
     * @param \Mollie\Client\Mollie\Api\ApiCallInterface $availablePaymentMethodsApi
     * @param \Mollie\Client\Mollie\MollieConfig $config
     * @param \Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $encodingService
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Client\Mollie\Dependency\Client\MollieToStoreClientInterface $storeClient
     */
    public function __construct(
        protected ApiCallInterface $availablePaymentMethodsApi,
        protected MollieConfig $config,
        protected MollieToUtilEncodingServiceInterface $encodingService,
        protected MollieToStorageClientInterface $storageClient,
        protected MollieToStoreClientInterface $storeClient,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer $mollieApiRequestTransfer
     *
     * @return void
     */
    protected function generateCacheKey(MollieApiRequestTransfer $mollieApiRequestTransfer): void
    {
        $storeName = $this->storeClient->getCurrentStore()->getName();
        $mode = $this->config->getMollieTestModeEnabled();
        $ttl = $this->config->getMolliePaymentMethodsStorageKeyTTL();
        $sequenceType = 'oneOff';
    }
}
