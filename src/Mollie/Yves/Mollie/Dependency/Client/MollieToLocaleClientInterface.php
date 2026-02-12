<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Dependency\Client;

interface MollieToLocaleClientInterface
{
    /**
     * @return string
     */
    public function getCurrentLocale(): string;
}
