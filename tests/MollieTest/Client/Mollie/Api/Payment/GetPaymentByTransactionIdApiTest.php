<?php


declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;
use MollieTest\Client\Mollie\MollieApiClientTester;

class GetPaymentByTransactionIdApiTest extends AbstractClientTest
{
     /**
      * @var \MollieTest\Client\Mollie\MollieApiClientTester
      */
    protected MollieApiClientTester $tester;

    /**
     * @return void
     */
    public function testGetPaymentByTransactionIdApi(): void
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setBody(['id' => 'tr_IUDAHSMGnU6qLbRaksas']);

        $client = $this->createClient();

        $response = $client->getPaymentByTransactionId($mollieApiRequestTransfer);

        $this->assertInstanceOf(MolliePaymentApiResponseTransfer::class, $response);
        $this->assertEquals(true, $response->getIsSuccessful());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(): MollieClientInterface
    {
         $mollieFactoryMock = $this->createMollieFactoryMock();
         $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForGetPaymentRequest());

         return $this->createClientMock($mollieFactoryMock);
    }

     /**
      * @return \Mollie\Api\Fake\MockMollieClient
      */
    public function createMockApiClientForGetPaymentRequest(): MockMollieClient
    {
        $response = [
            GetPaymentRequest::class => new MockResponse(
                $this->tester->getPaymentByTransactionId(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
