<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Deleter\Payment;

use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\StorageScanResultTransfer;
use Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientBridge;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieFactory;
use Mollie\Shared\Mollie\MollieConstants;
use MollieTest\Client\Mollie\AbstractClientTest;
use PHPUnit\Framework\MockObject\MockObject;

class PaymentMethodsCacheDeleterTest extends AbstractClientTest
{
 /**
  * @return void
  */
    public function testDeleteEnabledPaymentMethodsCacheRemovesItFromStorage(): void
    {
        // Arrange
        $parameters = $this->createMolliePaymentMethodQueryParametersTransfer();
        $factory = $this->createMollieFactoryMock();
        $cacheKeyPrefix = $factory->getConfig()->getCacheKeyPrefixForEnabledPaymentMethods();
        $cacheKey = $this->getCacheKey($factory, $parameters, $cacheKeyPrefix);
        $client = $this->createClient($factory, $cacheKey);

        $this->tester->getStorageClient()->set($cacheKey, $this->tester->getMollieMockedEnabledPaymentMethodResponsePayload());

        // Act
        $client->deleteEnabledPaymentMethodsCache($parameters);

        //Assert
        $this->tester->assertStorageNotHasKey($cacheKey);
    }

      /**
       * @return void
       */
    public function testDeleteAllPaymentMethodsCacheRemovesItFromStorage(): void
    {
        // Arrange
        $parameters = $this->createMolliePaymentMethodQueryParametersTransfer();
        $factory = $this->createMollieFactoryMock();
        $cacheKeyPrefix = $factory->getConfig()->getCacheKeyPrefixForAllPaymentMethods();
        $cacheKey = $this->getCacheKey($factory, $parameters, $cacheKeyPrefix);
        $client = $this->createClient($factory, $cacheKey);

        $this->tester->getStorageClient()->set($cacheKey, $this->tester->getMollieMockedAllPaymentMethodResponsePayload());

        // Act
        $client->deleteAllPaymentMethodsCache($parameters);

        //Assert
        $this->tester->assertStorageNotHasKey($cacheKey);
    }

     /**
      * @param \Mollie\Client\Mollie\MollieFactory|null $mollieFactory
      * @param string $cacheKey
      *
      * @return \Mollie\Client\Mollie\MollieClientInterface
      */
    public function createClient(?MockObject $mollieFactory, string $cacheKey): MollieClientInterface
    {
        $mollieFactory->method('getStorageClient')->willReturn($this->getMollieToStorageClientBridgeMock($cacheKey));

        return $this->createClientMock($mollieFactory);
    }

    /**
     * @param string $cacheKey
     *
     * @return \Mollie\Client\Mollie\Dependency\Client\MollieToStorageClientBridge
     */
    public function getMollieToStorageClientBridgeMock(string $cacheKey): MollieToStorageClientBridge
    {
        $mollieToStorageClientMock = $this->createMollieToStorageClientBridgeMock();

        $mollieToStorageClientMock->method('scanKeys')->willReturn(
            (new StorageScanResultTransfer())->setKeys([
                sprintf('kv:%s', $cacheKey),
            ]),
        );

        return $mollieToStorageClientMock;
    }

    /**
     * @param \Mollie\Client\Mollie\MollieFactory $mollieFactory
     * @param \Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer $parameters
     * @param string $cacheKeyPrefix
     *
     * @return string
     */
    protected function getCacheKey(
        MollieFactory $mollieFactory,
        MolliePaymentMethodQueryParametersTransfer $parameters,
        string $cacheKeyPrefix,
    ): string {
        $keyGenerator = $mollieFactory->createPaymentMethodsCacheKeyGenerator();
        $cacheKey = $keyGenerator->generateCacheKey($parameters, $cacheKeyPrefix);

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
