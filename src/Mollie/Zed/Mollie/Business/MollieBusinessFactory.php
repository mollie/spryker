<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandler;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapper;
use Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface;
use Mollie\Zed\Mollie\Business\Order\OrderUpdater;
use Mollie\Zed\Mollie\Business\Order\OrderUpdaterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 * @method \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface getEntityManager()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Business\Order\OrderUpdaterInterface
     */
    public function createPaymentStatusUpdater(): OrderUpdaterInterface
    {
        return new OrderUpdater(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->getOmsFacade(),
            $this->getLogger(),
            $this->createMollieOmsStatusMapper(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface
     */
    public function createMollieOmsStatusMapper(): MolleOmsStatusMapperInterface
    {
        return new MolleOmsStatusMapper(
            $this->getConfig(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface
     */
    public function getOmsFacade(): MollieToOmsInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::LOGGER);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function getMollieClient(): MollieClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_MOLLIE);
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface
     */
    public function createMolliePaymentHandler(): MolliePaymentHandlerInterface
    {
        return new MolliePaymentHandler(
            $this->getMollieClient(),
            $this->getStorageClient(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }
}
