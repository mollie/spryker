<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

interface MollieToTranslatorFacadeInterface
{
 /**
  * Specification:
  * - Translates the given message.
  *
  * @api
  *
  * @param string $translationKey
  * @param array<mixed> $parameters
  * @param string|null $domain
  * @param string|null $locale
  *
  * @return string
  */
    public function trans(string $translationKey, array $parameters = [], ?string $domain = null, ?string $locale = null): string;
}
