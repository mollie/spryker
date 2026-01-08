<?php

namespace MollieTest\Client\Mollie;

use Codeception\Test\Unit;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\MollieClient;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Client\Mollie\MollieDependencyProvider;
use Mollie\Client\Mollie\MollieFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Spryker\Client\Kernel\Container;

abstract class AbstractClientTest extends Unit
{
    /**
     * @param array $response
     *
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClient(array $response): MockMollieClient
    {
        $client = MollieApiClient::fake($response);

        $client->setApiKey('test_jdkshfdsk1213sjkadsdasfdsafdsfdsfdsfds2qeqasx');

        return $client;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieFactory
     */
    public function createMollieFactoryMock(): MollieFactory
    {
        $mollieFactoryMock = $this->getMockBuilder(MollieFactory::class)
            ->onlyMethods(['createMollieApiClient'])
            ->getMock();

        $mollieFactoryMock->setConfig(new MollieConfig());
        $dependencyProvider = new MollieDependencyProvider();

        $container = new Container();
        $dependencyProvider->provideServiceLayerDependencies($container);
        $mollieFactoryMock->setContainer($container);

        return $mollieFactoryMock;
    }

 /**
  * @param \Mollie\Client\Mollie\MollieFactory|null $mollieFactory
  *
  * @return \Mollie\Client\Mollie\MollieClientInterface
  */
    public function createClientMock(?MockObject $mollieFactory = null): MollieClientInterface
    {
        if (!$mollieFactory) {
            $mollieFactory = $this->createMollieFactoryMock();
        }

        $client = $this
            ->getMockBuilder(MollieClient::class)
            ->onlyMethods(['getFactory'])
            ->getMock();

        $client->expects($this->any())
            ->method('getFactory')
            ->willReturn($mollieFactory);

        return $client;
    }
}
