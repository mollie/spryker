<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Propel\Runtime\Collection\ObjectCollection;

interface MollieOrderItemMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieCollection
     *
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    public function extractOrderItemsFromSpyPaymentMollieEntity(ObjectCollection $spyPaymentMollieCollection): ObjectCollection;
}
