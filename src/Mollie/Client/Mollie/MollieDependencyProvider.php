<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceBridge;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class MollieDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const UTIL_ENCODING_SERVICE = 'UTIL_ENCODING_SERVICE';

    /**
     * @var string
     */
    public const MOLLIE_SERVICE = 'MOLLIE_SERVICE';

    /**
     * @var string
     */
    public const SERVICE_ZED = 'zed service';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addMollieService($container);
        $container = $this->addZedRequestClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::UTIL_ENCODING_SERVICE, function (Container $container): MollieToUtilEncodingServiceInterface {
            return new MollieToUtilEncodingServiceBridge(
                $container->getLocator()->utilEncoding()->service(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addMollieService(Container $container): Container
    {
        $container->set(static::MOLLIE_SERVICE, function (Container $container): MollieServiceInterface {
            return $container->getLocator()->mollie()->service();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addZedRequestClient(Container $container): Container
    {
        $container->set(static::SERVICE_ZED, function (Container $container) {
            return $container->getLocator()->zedRequest()->client();
        });

        return $container;
    }
}
