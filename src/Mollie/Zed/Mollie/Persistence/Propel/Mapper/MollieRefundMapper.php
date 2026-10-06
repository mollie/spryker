<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;

class MollieRefundMapper implements MollieRefundMapperInterface
{
    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function mapFromSpyRefundMollieEntityToMollieRefundTransfer(SpyRefundMollie $spyRefundMollieEntity): MollieRefundResponseTransfer
    {
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->fromArray($spyRefundMollieEntity->toArray(), true);

        return (new MollieRefundResponseTransfer())
            ->setRefund($mollieRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function mapToSpyRefundMollieEntity(
        MollieRefundSaveTransfer $mollieRefundSaveTransfer,
        SpyRefundMollie $spyRefundMollieEntity,
    ): SpyRefundMollie {
        $spyRefundMollieEntity
            ->setFkSalesOrderItem($mollieRefundSaveTransfer->getItem()->getIdSalesOrderItem())
            ->setDescription($mollieRefundSaveTransfer->getDescription())
            ->setCurrency($mollieRefundSaveTransfer->getCurrency())
            ->setValue($mollieRefundSaveTransfer->getValue())
            ->setStatus($mollieRefundSaveTransfer->getStatus())
            ->setMetadata($mollieRefundSaveTransfer->getMetadata())
            ->setTransactionId($mollieRefundSaveTransfer->getTransactionId())
            ->setRefundId($mollieRefundSaveTransfer->getRefundId())
            ->setCreatedAt($mollieRefundSaveTransfer->getCreatedAt());

        return $spyRefundMollieEntity;
    }
}
