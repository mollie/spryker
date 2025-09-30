<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\PaymentMethods;

use Codeception\Test\Unit;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\PaymentMethods\AvailablePaymentMethodsApi;
use Mollie\Client\Mollie\MollieClient;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieFactory;
use MollieTest\Client\Mollie\MollieApiClientTester;

class AvailablePaymentMethodsApiTest extends Unit
{
    /**
     * @var \MollieTest\Client\Mollie\MollieApiClientTester
     */
    protected MollieApiClientTester $tester;

 /**
  * @return \Mollie\Api\Fake\MockMollieClient
  */
    public function createMockApiClient(): MockMollieClient
    {
        $client = MollieApiClient::fake([
            GetEnabledMethodsRequest::class => new MockResponse(
                $this->tester->getMollieMockedPaymentMethodResponsePayload(),
            ),
        ]);

        $client->setApiKey('test_jdkshfdsk1213sjkadsdasfdsafdsfdsfdsfds2qeqasx');

        return $client;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createMollieClient(): MollieClientInterface
    {
        return (new MollieClient())
            ->setFactory(
                $this->createFactory(),
            );
    }

    /**
     * @return \Mollie\Client\Mollie\MollieFactory
     */
    protected function createFactory(): MollieFactory
    {
        $mockFactory = $this->tester->mockFactoryMethod('createAvailablePaymentMethodsApi', $this->createAvailablePaymentMethodApiMock());

        return $mockFactory;
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createAvailablePaymentMethodApiMock(): ApiCallInterface
    {
        $client = $this->createMockApiClient();
        $stub = new AvailablePaymentMethodsApi($client);

        return $stub;
    }

    /**
     * @return void
     */
    public function testGetAvailablePaymentMethods(): void
    {
        $client = $this->createMollieClient();
        $availablePaymentMethods = $client->getAvailablePaymentMethods();

        $this->assertNotEmpty($availablePaymentMethods->getMethods());
        $method = $availablePaymentMethods->getMethods()[0];

        $this->assertEquals('ideal', $method['id']);
        $this->assertEquals('iDEAL', $method['description']);
    }
}
