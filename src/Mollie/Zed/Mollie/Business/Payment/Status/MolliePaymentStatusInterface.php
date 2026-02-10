<?php

namespace Mollie\Zed\Mollie\Business\Payment\Status;

interface MolliePaymentStatusInterface
{
 /**
  * @param int $idSalesOrder
  * @param int $idSalesOrderItem
  *
  * @return bool
  */
    public function isAuthorizationFailed(int $idSalesOrder, int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isAuthorizationCanceled(int $idSalesOrder, int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isAuthorizationExpired(int $idSalesOrder, int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isAuthorized(int $idSalesOrder, int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptured(int $idSalesOrder, int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptureFailed(int $idSalesOrder, int $idSalesOrderItem): bool;
}
