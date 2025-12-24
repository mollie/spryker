<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie\Dependency\Service;

interface MollieToUtilEncodingServiceInterface
{
 /**
  * @param array<string, string> $value
  * @param int|null $options
  * @param int|null $depth
  *
  * @return string|null
  */
    public function encodeJson(array $value, ?int $options = null, ?int $depth = null): ?string;

   /**
    * @param string $jsonValue
    * @param bool $assoc
    * @param int|null $depth
    * @param int|null $options
    *
    * @return array<string, string>|null
    */
    public function decodeJson(string $jsonValue, bool $assoc = false, ?int $depth = null, ?int $options = null): ?array;
}
