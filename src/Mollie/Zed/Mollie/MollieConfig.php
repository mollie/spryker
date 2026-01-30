<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie;

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
     * @var string
     */
    public const REFUNDED = 'refunded';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT = 'checkout';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF = 'href';
}
