<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Service\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @return int
     */
    public function getPaymentLinkDefaultExpirationTime(): int
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PAYMENT_LINK_EXPIRATION_TIME];
    }
}
