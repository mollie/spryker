<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Service;

interface MollieToUtilEncodingServiceInterface
{
    /**
     * @param array<string, string> $value
     *
     * @return string|null
     */
    public function encodeJson(array $value): ?string;

    /**
     * @param string $jsonValue
     *
     * @return array<string, string>|null
     */
    public function decodeJson(string $jsonValue): ?array;
}
