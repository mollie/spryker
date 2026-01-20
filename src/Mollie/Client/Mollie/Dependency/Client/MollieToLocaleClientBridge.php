<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Dependency\Client;

use Spryker\Client\Locale\LocaleClientInterface;

class MollieToLocaleClientBridge implements MollieToLocaleClientInterface 
{
    /**
     * @param LocaleClientInterface $localeClient
     */
    public function __construct(LocaleClientInterface $localeClient)
    {
        $this->localeClient = $localeClient;
    }

    /**
     * @return string
     */
    public function getCurrentLocale(): string
    {
        return $this->localeClient->getCurrentLocale();
    }
}