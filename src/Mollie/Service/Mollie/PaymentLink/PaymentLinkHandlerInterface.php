<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie\PaymentLink;

interface PaymentLinkHandlerInterface
{
    /**
     * @return string
     */
    public function getPaymentLinkDefaultExpirationDateTime(): string;
}
