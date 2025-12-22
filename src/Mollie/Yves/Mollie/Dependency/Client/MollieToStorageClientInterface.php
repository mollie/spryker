<?php

namespace Mollie\Yves\Mollie\Dependency\Client;

interface MollieToStorageClientInterface
{
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
    public function set(string $key, mixed $value, ?int $ttl = null): mixed;

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
    public function get(string $key): mixed;

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
    public function delete(string $key): mixed;
}
