<?php


declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceBridge;
use Mollie\Client\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class MollieDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const UTIL_ENCODING_SERVICE = 'UTIL_ENCODING_SERVICE';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);

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
}
