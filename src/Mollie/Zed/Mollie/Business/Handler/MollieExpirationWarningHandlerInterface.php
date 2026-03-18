<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

interface MollieExpirationWarningHandlerInterface
{
    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function shouldDisplayExpiryWarning(int $orderId): bool;
}
