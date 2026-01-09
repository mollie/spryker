<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Propel\Runtime\Collection\ObjectCollection;

class MollieOrderItemMapper implements MollieOrderItemMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieCollection
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function extractOrderItemsFromSpyPaymentMollieEntity(ObjectCollection $spyPaymentMollieCollection): ObjectCollection|null
    {
        $spySalesOrderItems = null;

        foreach ($spyPaymentMollieCollection->getData() as $spyPaymentMollie) {
            $spySalesOrderItems = $spyPaymentMollie->getSpySalesOrder()->getItems();
        }

        return $spySalesOrderItems;
    }
}
