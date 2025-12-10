<?php

namespace Mollie\Glue\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Glue\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getMollieApiKey(): string
    {
        return MollieConstants::MOLLIE_API_KEY;
    }

    /**
     * @return string
     */
    public function getMollieTestModeEnabled(): string
    {
        return MollieConstants::MOLLIE_TEST_MODE;
    }
}
