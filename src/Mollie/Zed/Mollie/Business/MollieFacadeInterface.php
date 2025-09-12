<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

interface MollieFacadeInterface
{
    /**
     * @return string
     */
    public function getPaymentId(): string;
}
