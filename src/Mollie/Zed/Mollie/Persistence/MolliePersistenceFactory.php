<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderItemMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderItemMapperInterface;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class MolliePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderItemMapperInterface
     */
    public function createMollieOrderItemMapper(): MollieOrderItemMapperInterface
    {
        return new MollieOrderItemMapper();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery
     */
    public function createSpyPaymentMollieQuery(): SpyPaymentMollieQuery
    {
        return SpyPaymentMollieQuery::create();
    }
}
