<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

class MollieConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'MollieCreditCardPayment';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_INVOICE = 'molliePayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_CREDIT_CARD = 'mollieCreditCardPayment';
}
