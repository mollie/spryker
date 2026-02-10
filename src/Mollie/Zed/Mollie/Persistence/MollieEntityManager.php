<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollie;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieEntityManager extends AbstractEntityManager implements MollieEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return void
     */
    public function updateMolliePaymentWithStatus(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): void
    {
        $spyPaymentMolleEntity = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->findOneByTransactionId($updateOrderCollectionRequestTransfer->getId());

        if (!$spyPaymentMolleEntity) {
            return;
        }

        $spyPaymentMolleEntity->setStatus($updateOrderCollectionRequestTransfer->getStatus());
        $spyPaymentMolleEntity->save();
    }

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function addMolliePaymentData(int $idSalesOrder, MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $spyPaymentMollieEntity = new SpyPaymentMollie();
        $metadata = $this->getFactory()->getUtilEncodingService()->encodeJson($molliePaymentTransfer->getMetadata());
        $spyPaymentMollieEntity
            ->setFkSalesOrder($idSalesOrder)
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus())
            ->setIsCancelable($molliePaymentTransfer->getIsCancelable())
            ->setDescription($molliePaymentTransfer->getDescription())
            ->setSequenceType($molliePaymentTransfer->getSequenceType())
            ->setMetadata($metadata)
            ->setExpiresAt($molliePaymentTransfer->getExpiresAt())
            ->setCreatedAt($molliePaymentTransfer->getCreatedAt());

        $spyPaymentMollieEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return void
     */
    public function updateMollieRefundWithStatus(MollieRefundTransfer $mollieRefundTransfer): void
    {
        $spyRefundMollieEntityCollection = $this->getFactory()
            ->createSpyRefundMollieQuery()
            ->findByRefundId($mollieRefundTransfer->getId());

        if (!$spyRefundMollieEntityCollection) {
            return;
        }

        foreach ($spyRefundMollieEntityCollection as $spyRefundMollieEntity) {
            $spyRefundMollieEntity->setStatus($mollieRefundTransfer->getStatus());
            $spyRefundMollieEntity->save();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     *
     * @return void
     */
    public function createRefund(MollieRefundSaveTransfer $mollieRefundSaveTransfer): void
    {
        $spyRefundMollieEntity = new SpyRefundMollie();

        $spyRefundMollieEntity = $this->getFactory()
            ->createMollieRefundMapper()
            ->mapToSpyRefundMollieEntity($mollieRefundSaveTransfer, $spyRefundMollieEntity);

        $spyRefundMollieEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer
     *
     * @return void
     */
    public function createCapture(MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer): void
    {
        $spyMollieOrderItemPaymentCaptureEntity = new SpyMollieOrderItemPaymentCapture();

        $spyMollieOrderItemPaymentCaptureEntity = $this->getFactory()
            ->createMolliePaymentCaptureMapper()
            ->mapMollieOrderItemPaymentCaptureTransferToEntity($mollieItemPaymentCaptureTransfer, $spyMollieOrderItemPaymentCaptureEntity);

        $spyMollieOrderItemPaymentCaptureEntity->save();
    }
}
