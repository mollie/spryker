<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderItemMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderItemMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapperInterface;
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
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapperInterface
     */
    public function createMollieOrderMapper(): MollieOrderMapperInterface
    {
        return new MollieOrderMapper();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery
     */
    public function createSpyPaymentMollieQuery(): SpyPaymentMollieQuery
    {
        return SpyPaymentMollieQuery::create();
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
