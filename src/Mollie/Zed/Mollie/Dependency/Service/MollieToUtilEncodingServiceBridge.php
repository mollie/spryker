<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Service;

class MollieToUtilEncodingServiceBridge implements MollieToUtilEncodingServiceInterface
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
     * @param array<string, string> $value
     *
     * @return string|null
     */
    public function encodeJson(array $value): ?string
    {
        return $this->utilEncodingService->encodeJson($value);
    }

    /**
     * @param string $jsonValue
     *
     * @return array<string, string>|null
     */
    public function decodeJson(string $jsonValue): ?array
    {
        return $this->utilEncodingService->decodeJson($jsonValue, true);
    }
}
