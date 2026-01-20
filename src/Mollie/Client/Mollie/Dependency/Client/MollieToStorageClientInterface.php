<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Client;

interface MollieToStorageClientInterface
{
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
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void;
}
