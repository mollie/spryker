<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Dependency\Service;

class MollieWebhookBackendApiToUtilEncodingServiceBridge implements MollieWebhookBackendApiToUtilEncodingServiceInterface
{
    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct($utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param string $jsonValue
     * @param int|null $depth
     * @param int|null $options
     *
     * @return array<mixed>|null
     */
    public function decodeJson(string $jsonValue, ?int $depth = null, ?int $options = null): ?array
    {
        /** @phpstan-var array<mixed>|null */
        return $this->utilEncodingService->decodeJson($jsonValue, true, $depth, $options);
    }
}
