<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;

interface MollieRepositoryInterface
{
    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPaymentByOrderId(string $orderId): MolliePaymentTransfer;

    /**
     * @param int $orderItemId
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function findRefundByOrderItem(int $orderItemId): MollieRefundResponseTransfer;
}
