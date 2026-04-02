<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;

class MollieToTranslatorFacadeBridge implements MollieToTranslatorFacadeInterface
{
    /**
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct(
        private TranslatorFacadeInterface $translatorFacade,
    ) {
    }

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
    public function trans(string $translationKey, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translatorFacade->trans($translationKey, $parameters, $domain, $locale);
    }
}
