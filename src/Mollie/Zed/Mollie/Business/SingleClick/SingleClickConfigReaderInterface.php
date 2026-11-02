<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

interface SingleClickConfigReaderInterface
{
    /**
     * @return bool
     */
    public function isSingleClickPaymentEnabled(): bool;
}
