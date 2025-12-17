<?php

namespace Mollie\Shared\Mollie;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'MollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_CREDIT_CARD = 'mollieCreditCardPayment';
}
