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
    public function __construct($storageClient)
    {
        $this->storageClient = $storageClient;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int|null $ttl
     *
     * @return void
     */
    public function set(string $key, string $value, ?int $ttl = null): void
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

    /**
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void
    {
        $this->storageClient->delete($key);
    }
}
