<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\MollieExpirationInformationTransfer;

interface MollieExpirationWarningHandlerInterface
{
    /**
     * @param int $orderId
     *
     * @return \Generated\Shared\Transfer\MollieExpirationInformationTransfer
     */
    public function getExpirationInformation(int $orderId): MollieExpirationInformationTransfer;
}
