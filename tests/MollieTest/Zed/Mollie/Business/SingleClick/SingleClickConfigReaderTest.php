<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\SingleClick;

use Codeception\Test\Unit;
use Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReader;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;

class SingleClickConfigReaderTest extends Unit
{
    /**
     * @return void
     */
    public function testBackOfficeSettingOverridesConfigDefault(): void
    {
        $singleClickConfigReader = $this->createSingleClickConfigReader(false, true);

        $this->assertFalse($singleClickConfigReader->isSingleClickPaymentEnabled());
    }

    /**
     * @return void
     */
    public function testConfigDefaultIsUsedWhenBackOfficeSettingWasNeverSaved(): void
    {
        $singleClickConfigReader = $this->createSingleClickConfigReader(null, true);

        $this->assertTrue($singleClickConfigReader->isSingleClickPaymentEnabled());
    }

    /**
     * @param bool|null $persistentConfig
     * @param bool $defaultConfig
     *
     * @return \Mollie\Zed\Mollie\Business\SingleClick\SingleClickConfigReader
     */
    protected function createSingleClickConfigReader(?bool $persistentConfig, bool $defaultConfig): SingleClickConfigReader
    {
        $repositoryMock = $this->createMock(MollieRepositoryInterface::class);
        $repositoryMock->method('findSingleClickConfig')->willReturn($persistentConfig);

        $configMock = $this->createMock(MollieConfig::class);
        $configMock->method('isSingleClickPaymentEnabledByDefault')->willReturn($defaultConfig);

        return new SingleClickConfigReader($configMock, $repositoryMock);
    }
}
