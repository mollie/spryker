<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\OrderTransfer;

interface MollieMailHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendPaymentConfirmationMail(OrderTransfer $orderTransfer): void;
}
