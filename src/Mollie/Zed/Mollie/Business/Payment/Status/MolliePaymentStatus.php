<?php

namespace Mollie\Zed\Mollie\Business\Payment\Status;

class MolliePaymentStatus implements MolliePaymentStatusInterface
{
    public function isAuthorizationFailed(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }

    public function isAuthorizationCanceled(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }

    public function isAuthorizationExpired(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }

    public function isAuthorized(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }

    public function isCaptured(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }

    public function isCaptureFailed(int $idSalesOrder, int $idSalesOrderItem): bool
    {
        return false;
    }
}
