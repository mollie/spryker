<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence;

use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieExpressCheckoutConfigMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieExpressCheckoutConfigMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieOrderMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentCaptureMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentCaptureMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentLinkMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentMethodConfigMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentMethodConfigMapperInterface;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapper;
use Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapperInterface;
use Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutConfigQuery;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCaptureQuery;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfigQuery;
use Orm\Zed\Mollie\Persistence\SpyPaymentMollieQuery;
use Orm\Zed\Mollie\Persistence\SpyRefundMollieQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig();
 */
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
        return new MolliePaymentLinkMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieRefundMapperInterface
     */
    public function createMollieRefundMapper(): MollieRefundMapperInterface
    {
        return new MollieRefundMapper();
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MolliePaymentMethodConfigMapperInterface
     */
    public function createMolliePaymentMethodConfigMapper(): MolliePaymentMethodConfigMapperInterface
    {
        return new MolliePaymentMethodConfigMapper(
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Persistence\Propel\Mapper\MollieExpressCheckoutConfigMapperInterface
     */
    public function createMollieExpressCheckoutConfigMapper(): MollieExpressCheckoutConfigMapperInterface
    {
        return new MollieExpressCheckoutConfigMapper();
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
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentLinkQuery
     */
    public function createSpyMolliePaymentLinkQuery(): SpyMolliePaymentLinkQuery
    {
        return SpyMolliePaymentLinkQuery::create();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentMethodConfigQuery
     */
    public function createSpyMolliePaymentMethodConfigQuery(): SpyMolliePaymentMethodConfigQuery
    {
        return SpyMolliePaymentMethodConfigQuery::create();
    }

    /**
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutConfigQuery
     */
    public function createSpyMollieExpressCheckoutConfigQuery(): SpyMollieExpressCheckoutConfigQuery
    {
        return SpyMollieExpressCheckoutConfigQuery::create();
    }
}
