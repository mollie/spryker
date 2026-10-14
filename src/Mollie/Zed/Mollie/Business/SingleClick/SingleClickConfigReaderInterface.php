<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\SingleClick;

interface SingleClickConfigReaderInterface
{
    /**
     * Specification:
     * - Returns the Back Office setting when it was saved.
     * - Otherwise returns the `MOLLIE_SINGLE_CLICK_PAYMENTS_ENABLED` config value.
     *
     * @return bool
     */
    public function isSingleClickPaymentEnabled(): bool;
}
