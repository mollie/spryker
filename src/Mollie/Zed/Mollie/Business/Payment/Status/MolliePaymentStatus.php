<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\Status;

use Mollie\Shared\Mollie\MolliePaymentStatusConstants;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class MolliePaymentStatus implements MolliePaymentStatusInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     */
    public function __construct(protected MollieRepositoryInterface $mollieRepository)
    {
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationFailed(int $idSalesOrder): bool
    {
        $status = MolliePaymentStatusConstants::AUTHORIZATION_FAILED;
        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($idSalesOrder);

        if ($molliePaymentTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationCanceled(int $idSalesOrder): bool
    {
        $status = MolliePaymentStatusConstants::AUTHORIZATION_CANCELED;
        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($idSalesOrder);

        if ($molliePaymentTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorizationExpired(int $idSalesOrder): bool
    {
        $status = MolliePaymentStatusConstants::AUTHORIZATION_EXPIRED;
        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($idSalesOrder);

        if ($molliePaymentTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function isAuthorized(int $idSalesOrder): bool
    {
        $status = MolliePaymentStatusConstants::AUTHORIZED;
        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($idSalesOrder);
        if ($molliePaymentTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptured(int $idSalesOrderItem): bool
    {
        $status = MolliePaymentStatusConstants::CAPTURED;
        $mollieItemPaymentCaptureTransfer = $this->mollieRepository->getOrderItemPaymentCapture($idSalesOrderItem);

        if ($mollieItemPaymentCaptureTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isCaptureFailed(int $idSalesOrderItem): bool
    {
        $status = MolliePaymentStatusConstants::CAPTURE_FAILED;
        $mollieItemPaymentCaptureTransfer = $this->mollieRepository->getOrderItemPaymentCapture($idSalesOrderItem);

        if ($mollieItemPaymentCaptureTransfer?->getStatus() === $status) {
            return true;
        }

        return false;
    }
}
