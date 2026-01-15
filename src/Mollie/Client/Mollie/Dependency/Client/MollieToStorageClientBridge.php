<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Client;

use Spryker\Client\Storage\StorageClientInterface;

class MollieToStorageClientBridge implements MollieToStorageClientInterface
{
    protected StorageClientInterface $storageClient;

    /**
     * @param \Spryker\Client\Storage\StorageClientInterface $storageClient
     */
    public function __construct(StorageClientInterface $storageClient)
    {
        $this->storageClient = $storageClient;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl
     *
     * @return void
     */
    public function set(string $key, string $value, int $ttl): void
    {
        $this->storageClient->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->storageClient->get($key);
    }
}
