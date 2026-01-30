<?php


declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\StorageScanResultTransfer;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientBridge;
use Mollie\Client\Mollie\MollieClient;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use MollieTest\Client\Mollie\AbstractClientTest;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentMethodsCacheDeleterTest extends AbstractClientTest
{
    protected ?string $cacheKey = null;

    /**
     * @return void
     */
    public function testDeleteEnabledPaymentMethodsCacheRemovesCachedStorageData(): void
    {
        // Arrange
        $parameters = $this->createMolliePaymentMethodQueryParametersTransfer();
        $factory = $this->createMollieFactoryMock();
        $cacheKey = $this->getCacheKey($factory, $parameters);
        $client = $this->createClientMock($factory);

        $this->tester->getStorageClient()->set($cacheKey, $this->tester->getMollieMockedEnabledPaymentMethodResponsePayload());

        // Act
        $client->deleteEnabledPaymentMethodsCache($parameters);

        //Assert
        $this->tester->assertStorageNotHasKey($cacheKey);
    }

      /**
       * @return void
       */
    public function testDeleteAllPaymentMethodsCacheRemovesCachedStorageData(): void
    {
        // Arrange
        $parameters = $this->createMolliePaymentMethodQueryParametersTransfer();
        $factory = $this->createMollieFactoryMock();
        $cacheKey = $this->getCacheKey($factory, $parameters);
        $client = $this->createClientMock($factory);

        $this->tester->getStorageClient()->set($cacheKey, $this->tester->getMollieMockedAllPaymentMethodResponsePayload());

        // Act
        $client->deleteEnabledPaymentMethodsCache($parameters);

        //Assert
        $this->tester->assertStorageNotHasKey($cacheKey);
    }

     /**
      * @param \Mollie\Client\Mollie\MollieFactory|null $mollieFactory
      *
      * @return \Mollie\Client\Mollie\MollieClientInterface
      */
    public function createClientMock(?MockObject $mollieFactory = null): MollieClientInterface
    {
        $mollieFactory->method('getStorageClient')->willReturn($this->getMollieToStorageClientBridgeMock());

        $client = $this
            ->getMockBuilder(MollieClient::class)
            ->onlyMethods(['getFactory'])
            ->getMock();

        $client->expects($this->any())
            ->method('getFactory')
            ->willReturn($mollieFactory);

        return $client;
    }

    /**
     * @return \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientBridge
     */
    public function getMollieToStorageClientBridgeMock(): MollieToStorageClientBridge
    {
        $mollieToStorageClientMock = $this->createMollieToStorageClientBridgeMock();

        $mollieToStorageClientMock->method('scanKeys')->willReturn(
            (new StorageScanResultTransfer())->setKeys([
                sprintf('kv:%s', $this->cacheKey),
            ]),
        );

        return $mollieToStorageClientMock;
    }

    /**
     * @param \Mollie\Client\Mollie\MollieFactory $mollieFactory
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     *
     * @return string
     */
    protected function getCacheKey(MockObject $mollieFactory, MolliePaymentMethodQueryParametersTransfer $parameters): string
    {
        $keyGenerator = $mollieFactory->createPaymentMethodsCacheKeyGenerator();
        $cacheKeyPrefix = $mollieFactory->getConfig()->getCacheKeyPrefixForEnabledPaymentMethods();

        $cacheKey = $keyGenerator->generateCacheKey($parameters, $cacheKeyPrefix);
        $this->cacheKey = $cacheKey;

        return $cacheKey;
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer
     */
    protected function createMolliePaymentMethodQueryParametersTransfer(): MolliePaymentMethodQueryParametersTransfer
    {
        return (new MolliePaymentMethodQueryParametersTransfer())
            ->setLocale('en_US')
            ->setIncludeIssuers(true)
            ->setSequenceType(MollieConstants::MOLLIE_SEQUENCE_TYPE_ONE_OFF);
    }
}
