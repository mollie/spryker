<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandler;
use Mollie\Zed\Mollie\Business\Handler\MolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class MollieBusinessFactory extends AbstractBusinessFactory
{
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
