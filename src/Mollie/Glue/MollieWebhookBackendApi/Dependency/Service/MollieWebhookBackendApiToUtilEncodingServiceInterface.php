<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Service;

interface MollieWebhookBackendApiToUtilEncodingServiceInterface
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
