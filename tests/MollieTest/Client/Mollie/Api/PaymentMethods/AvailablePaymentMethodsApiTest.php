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

class AvailablePaymentMethodsApiTest extends Unit
{
    /**
     * @var array
     */
    public const MOLLIE_MOCKED_PAYMENT_METHOD_RESPONSE_PAYLOAD = [
       'count' => 2,
        '_embedded' => [
            'methods' => [
                [
                    'resource' => 'method',
                    'id' => 'ideal',
                    'description' => 'iDEAL',
                    'minimumAmount' => [
                        'value' => '0.01',
                        'currency' => 'EUR',
                    ],
                    'maximumAmount' => [
                        'value' => '50000.00',
                        'currency' => 'EUR',
                    ],
                    'image' => [
                        'size1x' => 'https://mollie.com/external/icons/payment-methods/ideal.png',
                        'size2x' => 'https://mollie.com/external/icons/payment-methods/ideal%402x.png',
                        'svg' => 'https://mollie.com/external/icons/payment-methods/ideal.svg',
                    ],
                    'status' => 'activated',
                    '_links' => [
                        'self' => [
                            'href' => '...',
                            'type' => 'application/hal+json',
                        ],
                    ],
                ],
                [
                    'resource' => 'method',
                    'id' => 'creditcard',
                    'description' => 'Credit card',
                    'minimumAmount' => [
                        'value' => '0.01',
                        'currency' => 'EUR',
                    ],
                    'maximumAmount' => [
                        'value' => '2000.00',
                        'currency' => 'EUR',
                    ],
                    'image' => [
                        'size1x' => 'https://mollie.com/external/icons/payment-methods/creditcard.png',
                        'size2x' => 'https://mollie.com/external/icons/payment-methods/creditcard%402x.png',
                        'svg' => 'https://mollie.com/external/icons/payment-methods/creditcard.svg',
                    ],
                    'status' => 'activated',
                    '_links' => [
                        'self' => [
                            'href' => '...',
                            'type' => 'application/hal+json',
                        ],
                    ],
                ],
            ],
        ],
        '_links' => [
            'self' => [
                'href' => '...',
                'type' => 'application/hal+json',
            ],
            'documentation' => [
                'href' => '...',
                'type' => 'text/html',
            ],
        ],
    ];

 /**
  * @return \Mollie\Api\Fake\MockMollieClient
  */
    public function createMockApiClient(): MockMollieClient
    {
        $client = MollieApiClient::fake([
            GetEnabledMethodsRequest::class => new MockResponse(
                static::MOLLIE_MOCKED_PAYMENT_METHOD_RESPONSE_PAYLOAD,
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
        $builder = $this->getMockBuilder(MollieFactory::class)->getMock();
        $builder->method('createAvailablePaymentMethodsApi')
            ->willReturn($this->createAvailablePaymentMethodApiMock());

        return $builder;
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
