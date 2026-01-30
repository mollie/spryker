<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return void
     */
    public function addMollieRefundData(
        OrderTransfer $orderTransfer,
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieRefundTransfer $mollieRefundTransfer,
    ): void {
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $spyRefundMollieEntity = new SpyRefundMollie();

            $spyRefundMollieEntity = $this->getFactory()
                ->createMollieRefundMapper()
                ->mapToSpyRefundMollieEntity($itemTransfer, $molliePaymentTransfer, $mollieRefundTransfer, $spyRefundMollieEntity);

            $spyRefundMollieEntity->save();
        }
    }
}
