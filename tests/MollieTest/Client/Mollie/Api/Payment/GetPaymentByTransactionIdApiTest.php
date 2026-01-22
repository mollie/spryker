<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
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
        $mollieApiRequestTransfer->setTransactionId('tr_IUDAHSMGnU6qLbRaksas');

        $client = $this->createClient();

        $molliePaymentApiResponseTransfer = $client->getPaymentByTransactionId($mollieApiRequestTransfer);
        $molliePaymentTransfer = $molliePaymentApiResponseTransfer->getMolliePayment();

        $this->assertEquals('payment', $molliePaymentTransfer->getResource());
        $this->assertEquals('tr_IUDAHSMGnU6qLbRaksas', $molliePaymentTransfer->getId());
        $this->assertEquals('open', $molliePaymentTransfer->getStatus());
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
                $this->tester->getMollieMockedPaymentTransactionResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
