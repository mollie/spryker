<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Cache;

interface MollieCacheInvalidatorInterface
{
    public function invalidateCache(): void;
}
