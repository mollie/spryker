<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Zed\Mollie\Business\Order\OrderUpdater;
use Mollie\Zed\Mollie\Business\Order\OrderUpdaterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 * @method \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface getEntityManager()
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
}
