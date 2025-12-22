<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class MolliePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery
     */
    public function createSpyPaymentMollieQuery(): SpyPaymentMollieQuery
    {
        return SpyPaymentMollieQuery::create();
    }
}
