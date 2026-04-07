<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieRepository extends AbstractRepository implements MollieRepositoryInterface
{
    /**
     * @param int $fkSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer|null
     */
    public function getPaymentByFkSalesOrder(int $fkSalesOrder): ?MolliePaymentTransfer
    {
        $spyPaymentMollieRecord = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->filterByFkSalesOrder($fkSalesOrder)
            ->findOne();

        if (!$spyPaymentMollieRecord) {
            return new MolliePaymentTransfer();
        }

        return $this->getFactory()
            ->createMollieOrderMapper()
            ->mapFromSpyPaymentMollieEntityToMolliePaymentTransfer($spyPaymentMollieRecord);
    }

    /**
     * @param int $orderItemId
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function findRefundByOrderItem(int $orderItemId): MollieRefundResponseTransfer
    {
        $spyRefundMollieRecord = $this->getFactory()
            ->createSpyRefundMollieQuery()
            ->filterByFkSalesOrderItem($orderItemId)
            ->findOne();

        return $this->getFactory()
            ->createMollieRefundMapper()
            ->mapFromSpyRefundMollieEntityToMollieRefundTransfer($spyRefundMollieRecord);
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer|null
     */
    public function getOrderItemPaymentCapture(int $idSalesOrderItem): ?MollieItemPaymentCaptureTransfer
    {
         $spyMollieOrderItemPaymentCaptureEntity = $this->getFactory()
            ->createSpyMollieOrderItemPaymentCaptureQuery()
            ->filterByFkSalesOrderItem($idSalesOrderItem)
            ->findOne();

        if (!$spyMollieOrderItemPaymentCaptureEntity) {
            return null;
        }

        return $this->getFactory()
            ->createMolliePaymentCaptureMapper()
            ->mapFromSpyMollieOrderItemPaymentCaptureEntityToTransfer($spyMollieOrderItemPaymentCaptureEntity);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer|null
     */
    public function getPaymentLinkByFkSalesOrder(int $idSalesOrder): ?MolliePaymentLinkTransfer
    {
        $spyMolliePaymentLinkEntity = $this->getFactory()
            ->createSpyMolliePaymentLinkQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->findOne();

        if (!$spyMolliePaymentLinkEntity) {
            return null;
        }

        return $this->getFactory()
            ->createMolliePaymentLinkMapper()
            ->mapMolliePaymentLinkEntityToTransfer($spyMolliePaymentLinkEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer
     */
    public function getPaymentMethodConfigCollection(MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer): MolliePaymentMethodConfigCollectionTransfer
    {
        $query = $this->getFactory()
            ->createSpyMolliePaymentMethodConfigQuery();

        if ($criteriaTransfer->getCurrencyCode()) {
            $query->filterByCurrencyCode($criteriaTransfer->getCurrencyCode());
        }

        $entities = $query->find();

        return $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigEntitiesToCollection($entities);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer|null
     */
    public function getPaymentMethodConfigByCriteria(
        MolliePaymentMethodConfigCriteriaTransfer $criteriaTransfer,
    ): ?MolliePaymentMethodConfigTransfer {
        $spyMolliePaymentMethodConfigEntity = $this->getFactory()
            ->createSpyMolliePaymentMethodConfigQuery()
            ->filterByMollieId($criteriaTransfer->getMollieId())
            ->filterByCurrencyCode($criteriaTransfer->getCurrencyCode())
            ->findOne();

        if (!$spyMolliePaymentMethodConfigEntity) {
            return null;
        }

        return $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigEntityToTransfer($spyMolliePaymentMethodConfigEntity);
    }
}
