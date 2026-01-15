<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Communication\Table\MolliePaymentMethodsTable;
use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacade getFacade();
 */
class MollieCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\MolliePaymentMethodsTable
     */
    public function createMolliePaymentMethodsTable()
    {
        return new MolliePaymentMethodsTable(
            $this->createMolliePaymentMethodsDataProvider(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider
     */
    public function createMolliePaymentMethodsDataProvider()
    {
        return new MolliePaymentMethodsDataProvider(
            $this->getMollieClient(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface
     */
    public function getStorageClient(): MollieToStorageClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function getMollieClient(): MollieClientInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::CLIENT_MOLLIE);
    }
}
