<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Dependency\Client;

interface MollieToLocaleClientInterface 
{
    /**
     * @return string
     */
    public function getCurrentLocale(): string;
}