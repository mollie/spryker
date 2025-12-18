<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getProfileId(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PROFILE_ID];
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }
}
