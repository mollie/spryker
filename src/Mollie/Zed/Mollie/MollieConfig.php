<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @return string|null
     */
    public function getMollieApiKey(): string|null
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_API_KEY];
    }

    /**
     * @return string
     */
    public function getMollieTestModeEnabled(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }
}
