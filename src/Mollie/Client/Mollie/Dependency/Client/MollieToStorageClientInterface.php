<?php

namespace Mollie\Client\Mollie\Dependency\Client;

interface MollieToStorageClientInterface
{
 /**
  * @param string $key
  * @param string $value
  * @param int $ttl
  *
  * @return void
  */
    public function set(string $key, string $value, int $ttl): void;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed;
}
