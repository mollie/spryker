<?php

namespace MollieTest\Zed\Mollie\Business\Facade;

use Codeception\Test\Unit;
use MollieTest\Zed\Mollie\Business\MollieFacadeTest;
use MollieTest\Zed\Mollie\FacadeBusinessTester;
use MollieTest\Zed\Mollie\Helper\MollieFacadeHelper;

class GetMollieApiKeyTest extends Unit
{
    /**
     * @var \MollieTest\Zed\Mollie\FacadeBusinessTester
     */
    protected FacadeBusinessTester $tester;

    /**
     * @var \MollieTest\Zed\Mollie\Business\MollieFacadeTest
     */
    protected MollieFacadeTest $mollieFacadeTest;

    /**
     * @var \MollieTest\Zed\Mollie\Helper\MollieFacadeHelper
     */
    protected MollieFacadeHelper $mollieFacadeHelper;

    /**
     * @return void
     */
    public function testApiKeyExists(): void
    {
        // Arrange
        $this->tester->mockConfigMethod('getMollieApiKey', 'test12345');

        $mollieApiKey = $this->tester->getModuleConfig('Mollie')->getMollieApiKey();

        // Act
        $mollieApiKeyExists = $this->tester->checkIfMollieApiKeyExists($mollieApiKey);

        // Assert
        $this->assertTrue($mollieApiKeyExists);
    }

    /**
     * @return void
     */
    public function testApiKeyDoesNotExists(): void
    {
        // Arrange
        $this->tester->mockConfigMethod('getMollieApiKey', '');

        $mollieApiKey = $this->tester->getModuleConfig('Mollie')->getMollieApiKey();

        // Act
        $mollieApiKeyExists = $this->tester->checkIfMollieApiKeyExists($mollieApiKey);

        // Assert
        $this->assertFalse($mollieApiKeyExists);
    }
}
