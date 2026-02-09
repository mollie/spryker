<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundRequestTransfer;
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
     * @param string $refundId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function getOrderItemsFromSpyMollieRefund(string $refundId): ObjectCollection|null
    {
        $spyRefundMollieCollection = $this->getFactory()
            ->createSpyRefundMollieQuery()
            ->filterByRefundId($refundId)
            ->joinWithSpySalesOrderItem()
            ->find();

        return $this->getFactory()
            ->createMollieOrderItemMapper()
            ->extractOrderItemsFromSpyRefundMollieEntity($spyRefundMollieCollection);
    }

    /**
     * @param int $fkSalesOrder
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPaymentByFkSalesOrder(int $fkSalesOrder): MolliePaymentTransfer
    {
        $spyPaymentMollieRecord = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->filterByFkSalesOrder($fkSalesOrder)
            ->findOne();

        return $this->getFactory()
            ->createMollieOrderMapper()
            ->mapFromSpyPaymentMollieEntityToMolliePaymentTransfer($spyPaymentMollieRecord);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundRequestTransfer $mollieRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function getPersistedRefundById(MollieRefundRequestTransfer $mollieRefundRequestTransfer): MollieRefundResponseTransfer
    {
        $spyRefundMollieRecord = $this->getFactory()
            ->createSpyRefundMollieQuery()
            ->filterByRefundId($mollieRefundRequestTransfer->getRefund()->getId())
            ->findOne();

        return $this->getFactory()
            ->createMollieRefundMapper()
            ->mapFromSpyRefundMollieEntityToMollieRefundTransfer($spyRefundMollieRecord);
    }
}
