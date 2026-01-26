<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
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
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPaymentByOrderId(string $orderId): MolliePaymentTransfer
    {
        $spyPaymentMollieRecord = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->filterByFkSalesOrder($orderId)
            ->find();

        return $this->getFactory()
            ->createMollieOrderMapper()
            ->mapFromSpyPaymentMollieEntityToMolliePaymentTransfer($spyPaymentMollieRecord);
    }
}
