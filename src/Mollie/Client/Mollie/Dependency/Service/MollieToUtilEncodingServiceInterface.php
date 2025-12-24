<?php

namespace Mollie\Client\Mollie\Dependency\Service;

interface MollieToUtilEncodingServiceInterface
{
    /**
     * @param string $jsonValue
     * @param int|null $depth
     * @param int|null $options
     *
     * @return array<mixed>|null
     */
    public function decodeJson(string $jsonValue, ?int $depth = null, ?int $options = null): ?array;
}
