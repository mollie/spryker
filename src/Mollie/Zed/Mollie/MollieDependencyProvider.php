<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class MollieDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_MOLLIE = 'CLIENT_MOLLIE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addMollieClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMollieClient(Container $container): Container
    {
        $container->set(static::CLIENT_MOLLIE, function (Container $container) {
            return $container->getLocator()->mollie()->client();
        });

        return $container;
    }
}
