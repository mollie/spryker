<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency;

class MollieToStorageClientBridge implements MollieToStorageClientInterface
{
    /**
     * @var \Spryker\Client\Storage\StorageClientInterface
     */
    protected $storageClient;

    /**
     * @param \Spryker\Client\Storage\StorageClientInterface $storageClient
     */
    public function __construct($storageClient)
    {
        $this->storageClient = $storageClient;
    }

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function set(string $key, mixed $value, ?int $ttl = null): mixed
    {
        return $this->storageClient->set($key, $value, $ttl);
    }

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->storageClient->get($key);
    }

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete(string $key): mixed
    {
        return $this->storageClient->delete($key);
    }
}
