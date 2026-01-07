<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Propel\Runtime\Collection\ObjectCollection;

class MollieOrderItemMapper implements MollieOrderItemMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieCollection
     *
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    public function extractOrderItemsFromSpyPaymentMollieEntity(ObjectCollection $spyPaymentMollieCollection): ObjectCollection
    {
        $spySalesOrderItems = null;

        foreach ($spyPaymentMollieCollection->getData() as $spyPaymentMollie) {
            $spySalesOrderItems = $spyPaymentMollie->getSpySalesOrder()->getItems();
        }

        return $spySalesOrderItems;
    }
}
