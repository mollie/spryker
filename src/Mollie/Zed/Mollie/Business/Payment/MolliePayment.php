<?php

namespace Mollie\Zed\Mollie\Business\Payment;

class MolliePayment implements MolliePaymentInterface
{
    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return 'mollie';
    }
}
