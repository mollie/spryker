<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieRepository extends AbstractRepository implements MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function getOrderItemsByPaymentId(string $paymentId): ObjectCollection|null
    {
        $spyPaymentMollieCollection = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->filterByTransactionId($paymentId)
            ->joinWithSpySalesOrder()
                ->useSpySalesOrderQuery()
                    ->joinWithItem()
                ->endUse()
            ->find();

        return $this->getFactory()
            ->createMollieOrderItemMapper()
            ->extractOrderItemsFromSpyPaymentMollieEntity($spyPaymentMollieCollection);
    }

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
            return null;
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
}
