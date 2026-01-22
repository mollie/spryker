<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PAID = 'paid';

    /**
     * @var string
     */
    public const AUTHORIZED = 'authorized';

    /**
     * @var string
     */
    public const EXPIRED = 'expired';

    /**
     * @var string
     */
    public const FAILED = 'failed';

    /**
     * @var string
     */
    public const CANCELED = 'canceled';

    /**
     * @return string
     */
    public function getMollieRedirectUrl(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_REDIRECT_URL];
    }
}
