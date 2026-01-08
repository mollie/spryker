<?php


declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieAvailablePaymentMethodsApiResponseTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;
use MollieTest\Client\Mollie\MollieApiClientTester;

class AvailablePaymentMethodsApiTest extends AbstractClientTest
{
    /**
     * @var \MollieTest\Client\Mollie\MollieApiClientTester
     */
    protected MollieApiClientTester $tester;

    /**
     * @return void
     */
    public function testGetAvailablePaymentMethodsApi(): void
    {
        $client = $this->createClient();
        $response = $client->getAvailablePaymentMethods();

        $this->assertInstanceOf(MollieAvailablePaymentMethodsApiResponseTransfer::class, $response);
        $this->assertTrue($response->getIsSuccessful());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createClient(): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForAvailablePaymentMethods());

        return $this->createClientMock($mollieFactoryMock);
    }

     /**
      * @return \Mollie\Api\Fake\MockMollieClient
      */
    public function createMockApiClientForAvailablePaymentMethods(): MockMollieClient
    {
        $response = [
            GetEnabledMethodsRequest::class => new MockResponse(
                $this->tester->getMollieMockedPaymentMethodResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
