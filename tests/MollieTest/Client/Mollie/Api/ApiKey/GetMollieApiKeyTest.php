<?php

namespace MollieTest\Client\Mollie\Api\ApiKey;

use Codeception\Test\Unit;
use MollieTest\Client\Mollie\MollieApiClientTester;

class GetMollieApiKeyTest extends Unit
{
    /**
     * @var \MollieTest\Client\Mollie\MollieApiClientTester
     */
    protected MollieApiClientTester $tester;

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
