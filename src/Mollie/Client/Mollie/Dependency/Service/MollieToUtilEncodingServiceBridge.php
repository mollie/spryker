<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Service;

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
     * @param int|null $options
     * @param int|null $depth
     *
     * @return string|null
     */
    public function encodeJson(array $value, ?int $options = null, ?int $depth = null): ?string
    {
        return $this->utilEncodingService->encodeJson($value, $options, $depth);
    }

    /**
     * @param string $jsonValue
     * @param bool $assoc
     * @param int|null $depth
     * @param int|null $options
     *
     * @return array<string, string>|null
     */
    public function decodeJson(string $jsonValue, bool $assoc = false, ?int $depth = null, ?int $options = null): ?array
    {
        if ($assoc === false) {
            trigger_error(
                'Param #2 `$assoc` must be `true` as return of type `object` is not accepted.',
                E_USER_DEPRECATED,
            );
        }

        /** @phpstan-var array<mixed>|null */
        return $this->utilEncodingService->decodeJson($jsonValue, $assoc, $depth, $options);
    }
}
