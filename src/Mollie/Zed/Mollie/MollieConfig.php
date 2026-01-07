<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT = 'checkout';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF = 'href';
}
