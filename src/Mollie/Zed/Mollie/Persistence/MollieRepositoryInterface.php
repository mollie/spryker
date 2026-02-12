<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundRequestTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Propel\Runtime\Collection\ObjectCollection;

interface MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function getOrderItemsByPaymentId(string $paymentId): ObjectCollection|null;

    /**
     * @param string $refundId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function getOrderItemsFromSpyMollieRefund(string $refundId): ObjectCollection|null;

    /**
     * @param int $fkSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer|null
     */
    public function getPaymentByFkSalesOrder(int $fkSalesOrder): ?MolliePaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieRefundRequestTransfer $mollieRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function getPersistedRefundById(MollieRefundRequestTransfer $mollieRefundRequestTransfer): MollieRefundResponseTransfer;

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer|null
     */
    public function getOrderItemPaymentCapture(int $idSalesOrderItem): ?MollieItemPaymentCaptureTransfer;
}
