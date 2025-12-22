<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;

interface MollieEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return void
     */
    public function updateMolliePaymentWithStatus(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): void;
}
