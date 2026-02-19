<?php

namespace MollieTest\Client\Mollie\Api\Capture;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class GetPaymentCaptureApiTest extends AbstractClientTest
{
     /**
      * @return void
      */
    public function testGetCapturePaymentApi(): void
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $molliePaymentCaptureTransfer = new MolliePaymentCaptureTransfer();

        $mollieAmountTransfer = new MollieAmountTransfer();
        $mollieAmountTransfer
            ->setValue('35.95')
            ->setCurrency('EUR');

        $molliePaymentCaptureTransfer
            ->setId('cpt_vytxeTZskVKR7C7WgdSP3d')
            ->setTransactionId('tr_5B8cwPMGnU6qLbRvo7qEZo')
            ->setDescription('Capture for cart #12345')
            ->setAmount($mollieAmountTransfer);

        $client = $this->createClient();
        $mollieApiRequestTransfer->setPaymentCapture($molliePaymentCaptureTransfer);
        $mollieCreateCaptureApiResponseTransfer = $client->getCapture($mollieApiRequestTransfer);
        $captureTransfer = $mollieCreateCaptureApiResponseTransfer->getPaymentCapture();

        $this->assertEquals('capture', $captureTransfer->getResource());
        $this->assertEquals('cpt_vytxeTZskVKR7C7WgdSP3d', $captureTransfer->getId());
        $this->assertEquals('pending', $captureTransfer->getStatus());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(): MollieClientInterface
    {
         $mollieFactoryMock = $this->createMollieFactoryMock();
         $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForGetCapturePaymentRequest());

         return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForGetCapturePaymentRequest(): MockMollieClient
    {
        $response = [
            GetPaymentCaptureRequest::class => new MockResponse(
                $this->tester->getMollieMockedGetCaptureResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
