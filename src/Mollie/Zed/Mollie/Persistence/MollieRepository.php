<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodConfigTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
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

    public function getPaymentMethodConfigCollection(?int $localeId): MolliePaymentMethodConfigCollectionTransfer
    {
        $query = $this->getFactory()
            ->createSpyMolliePaymentMethodConfigQuery()
            ->joinWithSpyMolliePaymentMethodConfigTranslation(Criteria::LEFT_JOIN)
        ;

        if ($localeId) {
//            $query->addJoinCondition(
//                'SpyMolliePaymentMethodConfigTranslation',
//                'SpyMolliePaymentMethodConfigTranslation.FkLocale = ?',
//                $localeId
//            );
        }

        $entities = $query->find();

        return $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigEntitiesToCollection($entities);
    }

    /**
     * @param string $mollieKey
     *
     * @return MolliePaymentMethodConfigTransfer|null
     */
    public function getMolliePaymentMethodConfigByMollieKey(string $mollieKey): ?MolliePaymentMethodConfigTransfer
    {
        $spyMolliePaymentLinkEntity = $this->getFactory()
            ->createSpyMolliePaymentMethodConfigQuery()
            ->filterByPaymentMethodKey($mollieKey)
            ->joinWithSpyMolliePaymentMethodConfigTranslation(Criteria::LEFT_JOIN)
            ->find()
            ->getFirst();

        if (!$spyMolliePaymentLinkEntity) {
            return null;
        }

        return $this->getFactory()
            ->createMolliePaymentMethodConfigMapper()
            ->mapMolliePaymentMethodConfigEntityToTransfer($spyMolliePaymentLinkEntity);
    }
}
