<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\Status;

interface MolliePaymentStatusInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationFailed(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationCanceled(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationExpired(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorized(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptured(int $idSalesOrderItem): bool;

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptureFailed(int $idSalesOrderItem): bool;
}
