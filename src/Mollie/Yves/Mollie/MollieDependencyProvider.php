<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class MollieDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_STORAGE = 'CLIENT_STORAGE';

    /**
     * @var string
     */
    public const CLIENT_MOLLIE = 'CLIENT_MOLLIE';

    /**
     * @var string
     */
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container = $this->addMollieToStorageClientBridge($container);
        $container = $this->addMollieClient($container);
        $container = $this->addQuoteClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMollieToStorageClientBridge(Container $container): Container
    {
        $container->set(static::CLIENT_STORAGE, function (Container $container): MollieToStorageClientInterface {
            return new MollieToStorageClientBridge(
                $container->getLocator()->storage()->client(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMollieClient(Container $container): Container
    {
        $container->set(static::CLIENT_MOLLIE, function (Container $container): MollieClientInterface {
            return $container->getLocator()->mollie()->client();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUOTE, function (Container $container): MollieToQuoteClientInterface {
            return new MollieToQuoteClientBridge(
                $container->getLocator()->quote()->client(),
            );
        });

        return $container;
    }
}
