<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToQuoteClientInterface;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientBridge;
use Mollie\Yves\Mollie\Dependency\Client\MollieToStorageClientInterface;
use Mollie\Yves\Mollie\Dependency\Service\MollieToUtilEncodingServiceBridge;
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
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

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
        $container = $this->addUtilEncodingService($container);

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

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new MollieToUtilEncodingServiceBridge(
                $container->getLocator()->utilEncoding()->service(),
            );
        });

        return $container;
    }
}
