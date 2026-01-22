<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidator;
use Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidatorInterface;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapper;
use Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface;
use Mollie\Zed\Mollie\Communication\Table\MolliePaymentMethodsTable;
use Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface;
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
    public function createMolliePaymentMethodsTable(): MolliePaymentMethodsTable
    {
        return new MolliePaymentMethodsTable(
            $this->createMolliePaymentMethodsDataProvider(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Table\TableDataProvider\MolliePaymentMethodsDataProvider
     */
    public function createMolliePaymentMethodsDataProvider(): MolliePaymentMethodsDataProvider
    {
        return new MolliePaymentMethodsDataProvider(
            $this->createMollieCommunicationMapper(),
            $this->getMollieClient(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Cache\MollieCacheInvalidatorInterface
     */
    public function createMollieCacheInvalidator(): MollieCacheInvalidatorInterface
    {
        return new MollieCacheInvalidator(
            $this->createMollieCommunicationMapper(),
            $this->getMollieClient(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \Mollie\Zed\Mollie\Communication\Mapper\MollieCommunicationMapperInterface
     */
    public function createMollieCommunicationMapper(): MollieCommunicationMapperInterface
    {
        return new MollieCommunicationMapper();
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

    /**
     * @return \Mollie\Zed\Mollie\Dependency\Facade\MollieToLocaleFacadeInterface
     */
    public function getLocaleFacade(): MollieToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(MollieDependencyProvider::FACADE_LOCALE);
    }
}
