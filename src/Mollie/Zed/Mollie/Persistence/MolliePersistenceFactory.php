<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence;

use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentCaptureMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentCaptureMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapperInterface;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCaptureQuery;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;
use Orm\Zed\Mollie\Persistence\SpyRefundMollieQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class MolliePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentCaptureMapperInterface
     */
    public function createMolliePaymentCaptureMapper(): MolliePaymentCaptureMapperInterface
    {
        return new MolliePaymentCaptureMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapperInterface
     */
    public function createMollieOrderMapper(): MollieOrderMapperInterface
    {
        return new MollieOrderMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapperInterface
     */
    public function createMolliePaymentLinkMapper(): MolliePaymentLinkMapperInterface
    {
        return new MolliePaymentLinkMapper(
            $this->getMoneyFacade(),
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapperInterface
     */
    public function createMollieRefundMapper(): MollieRefundMapperInterface
    {
        return new MollieRefundMapper(
            $this->getUtilEncodingService(),
            $this->getMollieService(),
        );
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery
     */
    public function createSpyPaymentMollieQuery(): SpyPaymentMollieQuery
    {
        return SpyPaymentMollieQuery::create();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCaptureQuery
     */
    public function createSpyMollieOrderItemPaymentCaptureQuery(): SpyMollieOrderItemPaymentCaptureQuery
    {
        return SpyMollieOrderItemPaymentCaptureQuery::create();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollieQuery
     */
    public function createSpyRefundMollieQuery(): SpyRefundMollieQuery
    {
        return SpyRefundMollieQuery::create();
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MollieToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \Mollie\Service\Mollie\MollieServiceInterface
     */
    public function getMollieService(): MollieServiceInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::SERVICE_MOLLIE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToMoneyFacadeInterface
     */
    public function getMoneyFacade(): MollieToMoneyFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_MONEY);
    }
}
