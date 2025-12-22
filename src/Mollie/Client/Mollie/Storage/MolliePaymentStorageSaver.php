<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Storage;

use Mollie\Client\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentStorageSaver implements MolliePaymentStorageSaverInterface
{
    /**
     * @param \Mollie\Client\Mollie\Dependency\MollieToStorageClientInterface $storageClient
     */
    public function __construct(
        protected MollieToStorageClientInterface $storageClient,
    ) {
    }

    /**
     * @param string $orderReference
     * @param string $paymentId
     *
     * @return void
     */
    public function savePaymentIdKey(string $orderReference, string $paymentId): void
    {
        $key = sprintf('%s:%s', MollieConfig::MOLLIE_STORAGE_KEY_PREFIX, $orderReference);
        $this->storageClient->set($key, $paymentId, MollieConfig::MOLLIE_STORAGE_TTL);
    }
}
