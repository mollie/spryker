<?php

namespace MollieTest\Zed\Mollie\Business\Facade;

use Codeception\Test\Unit;
use MollieTest\Zed\Mollie\FacadeBusinessTester;

class GetMollieApiKeyTest extends Unit
{
    /**
     * @var \MollieTest\Zed\Mollie\FacadeBusinessTester
     */
    protected FacadeBusinessTester $tester;

    /**
     * @return void
     */
    public function testApiKeyExists(): void
    {
        // Arrange
        /**
         * @var \Mollie\Shared\Mollie\MollieConfig $mollieConfig
         */
        $mollieConfig = $this->tester->getSharedModuleConfig('Mollie');

        // Act
        $mollieApiKey = $mollieConfig->getMollieApiKey();

        // Assert
        $this->assertNotNull($mollieApiKey);
        $this->assertNotEmpty($mollieApiKey);
    }
}
