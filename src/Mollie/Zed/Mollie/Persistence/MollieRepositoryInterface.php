<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;

interface MollieRepositoryInterface
{
    /**
     * @param int $fkSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer|null
     */
    public function getPaymentByFkSalesOrder(int $fkSalesOrder): ?MolliePaymentTransfer;

    /**
     * @param int $orderItemId
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function findRefundByOrderItem(int $orderItemId): MollieRefundResponseTransfer;

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer|null
     */
    public function getOrderItemPaymentCapture(int $idSalesOrderItem): ?MollieItemPaymentCaptureTransfer;
}
