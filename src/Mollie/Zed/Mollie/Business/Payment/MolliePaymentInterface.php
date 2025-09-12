<?php

namespace Mollie\Zed\Mollie\Business\Payment;

interface MolliePaymentInterface
{
    /**
     * @return string
     */
    public function getPaymentId(): string;
}
