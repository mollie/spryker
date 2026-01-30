<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Logger;

use Generated\Shared\Transfer\MollieLogApiTransfer;

interface MollieLoggerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    public function logResponse(MollieLogApiTransfer $logApiTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\MollieLogApiTransfer $logApiTransfer
     *
     * @return void
     */
    public function logMessage(MollieLogApiTransfer $logApiTransfer): void;
}
