<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder;

class MollieExpressCheckoutFailedOrderMapper implements MollieExpressCheckoutFailedOrderMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder
     */
    public function mapTransferToEntity(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
        SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity,
    ): SpyMollieExpressCheckoutFailedOrder {
        return $spyMollieExpressCheckoutFailedOrderEntity
            ->setExpressCheckoutUuid($mollieExpressCheckoutFailedOrderTransfer->getExpressCheckoutUuidOrFail())
            ->setMollieSessionId($mollieExpressCheckoutFailedOrderTransfer->getMollieSessionIdOrFail())
            ->setTransactionId($mollieExpressCheckoutFailedOrderTransfer->getTransactionId())
            ->setStatus($mollieExpressCheckoutFailedOrderTransfer->getStatusOrFail())
            ->setAmount($mollieExpressCheckoutFailedOrderTransfer->getAmount())
            ->setCurrency($mollieExpressCheckoutFailedOrderTransfer->getCurrency())
            ->setRefundId($mollieExpressCheckoutFailedOrderTransfer->getRefundId());
    }

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    public function mapEntityToTransfer(
        SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity,
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer {
        return $mollieExpressCheckoutFailedOrderTransfer
            ->setIdMollieExpressCheckoutFailedOrder($spyMollieExpressCheckoutFailedOrderEntity->getIdMollieExpressCheckoutFailedOrder())
            ->setExpressCheckoutUuid($spyMollieExpressCheckoutFailedOrderEntity->getExpressCheckoutUuid())
            ->setMollieSessionId($spyMollieExpressCheckoutFailedOrderEntity->getMollieSessionId())
            ->setTransactionId($spyMollieExpressCheckoutFailedOrderEntity->getTransactionId())
            ->setStatus($spyMollieExpressCheckoutFailedOrderEntity->getStatus())
            ->setAmount($spyMollieExpressCheckoutFailedOrderEntity->getAmount())
            ->setCurrency($spyMollieExpressCheckoutFailedOrderEntity->getCurrency())
            ->setRefundId($spyMollieExpressCheckoutFailedOrderEntity->getRefundId());
    }
}
