<?php

declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigReader;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutConfigReaderTest
 */
class ExpressCheckoutConfigReaderTest extends Unit
{
    /**
     * @var string
     */
    protected const METHOD_APPLE_PAY = 'applepay';

    /**
     * @var string
     */
    protected const METHOD_GOOGLE_PAY = 'googlepay';

    /**
     * @var string
     */
    protected const METHOD_PAYPAL = 'paypal';

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionReturnsOneEntryPerConfiguredMethod(): void
    {
        $defaultConfig = [
            static::METHOD_APPLE_PAY => false,
            static::METHOD_GOOGLE_PAY => false,
            static::METHOD_PAYPAL => false,
        ];

        $reader = $this->createReader($defaultConfig, []);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection());

        $this->assertSame(
            [static::METHOD_APPLE_PAY, static::METHOD_GOOGLE_PAY, static::METHOD_PAYPAL],
            array_keys($enabledByMethod),
        );
        $this->assertFalse($enabledByMethod[static::METHOD_APPLE_PAY]);
        $this->assertFalse($enabledByMethod[static::METHOD_GOOGLE_PAY]);
        $this->assertFalse($enabledByMethod[static::METHOD_PAYPAL]);
    }

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionUsesDefaultConfigWhenPersistentConfigIsEmpty(): void
    {
        $defaultConfig = [
            static::METHOD_APPLE_PAY => true,
            static::METHOD_GOOGLE_PAY => false,
            static::METHOD_PAYPAL => false,
        ];

        $reader = $this->createReader($defaultConfig, []);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection());

        $this->assertTrue($enabledByMethod[static::METHOD_APPLE_PAY]);
        $this->assertFalse($enabledByMethod[static::METHOD_GOOGLE_PAY]);
        $this->assertFalse($enabledByMethod[static::METHOD_PAYPAL]);
    }

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionPersistentConfigOverridesDefaultConfig(): void
    {
        $defaultConfig = [static::METHOD_APPLE_PAY => true, static::METHOD_GOOGLE_PAY => false];
        $persistentConfig = [static::METHOD_APPLE_PAY => false, static::METHOD_GOOGLE_PAY => true];

        $reader = $this->createReader($defaultConfig, $persistentConfig);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection());

        $this->assertFalse($enabledByMethod[static::METHOD_APPLE_PAY]);
        $this->assertTrue($enabledByMethod[static::METHOD_GOOGLE_PAY]);
    }

    /**
     * @param array<string, bool> $defaultConfig
     * @param array<string, bool> $persistentConfig
     *
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\ExpressCheckoutConfigReader
     */
    protected function createReader(array $defaultConfig, array $persistentConfig): ExpressCheckoutConfigReader
    {
        $configMock = $this->getMockBuilder(MollieConfig::class)
            ->onlyMethods(['getDefaultExpressCheckoutMethodConfig'])
            ->getMock();
        $configMock->method('getDefaultExpressCheckoutMethodConfig')->willReturn($defaultConfig);

        $repositoryMock = $this->getMockBuilder(MollieRepositoryInterface::class)->getMock();
        $repositoryMock->method('getPersistentExpressCheckoutMethodConfig')->willReturn($persistentConfig);

        return new ExpressCheckoutConfigReader($configMock, $repositoryMock);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutConfigCollectionTransfer $collectionTransfer
     *
     * @return array<string, bool>
     */
    protected function getEnabledByMethod($collectionTransfer): array
    {
        $enabledByMethod = [];
        foreach ($collectionTransfer->getConfigs() as $configTransfer) {
            $enabledByMethod[$configTransfer->getMethod()] = $configTransfer->getIsEnabled();
        }

        return $enabledByMethod;
    }
}
