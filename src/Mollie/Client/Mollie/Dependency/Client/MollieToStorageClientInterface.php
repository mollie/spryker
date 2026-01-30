<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Client;

use Generated\Shared\Transfer\StorageScanResultTransfer;

interface MollieToStorageClientInterface
{
   /**
    * @param string $pattern
    * @param int $limit
    * @param int|null $cursor
    *
    * @return \Generated\Shared\Transfer\StorageScanResultTransfer
    */
    public function scanKeys(string $pattern, int $limit, ?int $cursor = 0): StorageScanResultTransfer;

    /**
     * @param string $key
     * @param string $value
     * @param int|null $ttl
     *
     * @return void
     */
    public function set(string $key, string $value, ?int $ttl = null): void;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * @param array<string> $keys
     *
     * @return void
     */
    public function deleteMulti(array $keys): void;
}
