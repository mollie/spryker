<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Client\Mollie\Dependency\MollieToStorageClientBridge;
use Mollie\Client\Mollie\Dependency\MollieToStorageClientInterface;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class MollieDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_STORAGE = 'CLIENT_STORAGE';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->addStorageClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_STORAGE, function (Container $container): MollieToStorageClientInterface {
            return new MollieToStorageClientBridge(
                $container->getLocator()->storage()->client(),
            );
        });

        return $container;
    }
}
