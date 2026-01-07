<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

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
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    public function getOrderItemsByPaymentId(string $paymentId): ObjectCollection
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
}
