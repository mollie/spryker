<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieExpressCheckoutConfigCriteriaTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
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
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionReturnsOneEntryPerConfiguredMethod(): void
    {
        $defaultConfig = [
            SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY => false,
            SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY => false,
            SharedMollieConfig::EXPRESS_METHOD_PAYPAL => false,
        ];

        $reader = $this->createReader($defaultConfig, []);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection(new MollieExpressCheckoutConfigCriteriaTransfer()));

        $this->assertSame(
            [SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY, SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY, SharedMollieConfig::EXPRESS_METHOD_PAYPAL],
            array_keys($enabledByMethod),
        );
        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY]);
        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY]);
        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_PAYPAL]);
    }

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionUsesDefaultConfigWhenPersistentConfigIsEmpty(): void
    {
        $defaultConfig = [
            SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY => true,
            SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY => false,
            SharedMollieConfig::EXPRESS_METHOD_PAYPAL => false,
        ];

        $reader = $this->createReader($defaultConfig, []);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection(new MollieExpressCheckoutConfigCriteriaTransfer()));

        $this->assertTrue($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY]);
        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY]);
        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_PAYPAL]);
    }

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionPersistentConfigOverridesDefaultConfig(): void
    {
        $defaultConfig = [SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY => true, SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY => false];
        $persistentConfig = [SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY => false, SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY => true];

        $reader = $this->createReader($defaultConfig, $persistentConfig);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection(new MollieExpressCheckoutConfigCriteriaTransfer()));

        $this->assertFalse($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY]);
        $this->assertTrue($enabledByMethod[SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY]);
    }

    /**
     * @return void
     */
    public function testGetExpressCheckoutConfigCollectionFiltersByExpressMethodWhenCriteriaIsSet(): void
    {
        $defaultConfig = [
            SharedMollieConfig::EXPRESS_METHOD_APPLE_PAY => true,
            SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY => true,
            SharedMollieConfig::EXPRESS_METHOD_PAYPAL => true,
        ];

        $reader = $this->createReader($defaultConfig, []);

        $criteriaTransfer = (new MollieExpressCheckoutConfigCriteriaTransfer())
            ->setExpressMethod(SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY);

        $enabledByMethod = $this->getEnabledByMethod($reader->getExpressCheckoutConfigCollection($criteriaTransfer));

        $this->assertSame([SharedMollieConfig::EXPRESS_METHOD_GOOGLE_PAY], array_keys($enabledByMethod));
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
            ->onlyMethods(['getExpressMethods', 'getDefaultExpressCheckoutMethodConfig'])
            ->getMock();
        $configMock->method('getExpressMethods')->willReturn(SharedMollieConfig::EXPRESS_METHODS);
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
