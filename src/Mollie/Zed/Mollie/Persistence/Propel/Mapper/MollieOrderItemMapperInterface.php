<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Propel\Runtime\Collection\ObjectCollection;

interface MollieOrderItemMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyPaymentMollieCollection
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function extractOrderItemsFromSpyPaymentMollieEntity(ObjectCollection $spyPaymentMollieCollection): ObjectCollection|null;

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyRefundMollieCollection
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function extractOrderItemsFromSpyRefundMollieEntity(ObjectCollection $spyRefundMollieCollection): ObjectCollection|null;
}
