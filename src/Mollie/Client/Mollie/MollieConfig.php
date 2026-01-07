<?php

namespace Mollie\Client\Mollie;

use Spryker\Client\Kernel\AbstractBundleConfig;

/**
 * @method \Mollie\Shared\Mollie\MollieConfig getSharedConfig()()
 */
class MollieConfig extends AbstractBundleConfig
{
    /**
     * @return string|null
     */
    public function getMollieApiKey(): string|null
    {
        return $this->getSharedConfig()->getMollieApiKey();
    }
}
