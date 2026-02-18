<?php

namespace MollieTest\Client\Mollie\Api\Capture;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreatePaymentCaptureApiTest extends AbstractClientTest
{
 /**
  * @return void
  */
    public function testCreatePaymentCaptureApi(): void
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $molliePaymentCaptureTransfer = new MolliePaymentCaptureTransfer();

        $mollieAmountTransfer = new MollieAmountTransfer();
        $mollieAmountTransfer
            ->setValue('35.95')
            ->setCurrency('EUR');

        $molliePaymentCaptureTransfer
            ->setTransactionId('tr_5B8cwPMGnU6qLbRvo7qEZo')
            ->setDescription('Capture for cart #12345')
            ->setAmount($mollieAmountTransfer);

        $client = $this->createClient();
        $mollieApiRequestTransfer->setPaymentCapture($molliePaymentCaptureTransfer);
        $mollieCreateCaptureApiResponseTransfer = $client->createCapture($mollieApiRequestTransfer);
        $captureTransfer = $mollieCreateCaptureApiResponseTransfer->getPaymentCapture();

        $this->assertEquals('capture', $captureTransfer->getResource());
        $this->assertEquals('tr_5B8cwPMGnU6qLbRvo7qEZo', $captureTransfer->getTransactionId());
        $this->assertEquals('pending', $captureTransfer->getStatus());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(): MollieClientInterface
    {
         $mollieFactoryMock = $this->createMollieFactoryMock();
         $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForCreateCapturePaymentRequest());

         return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForCreateCapturePaymentRequest(): MockMollieClient
    {
        $response = [
            CreatePaymentCaptureRequest::class => new MockResponse(
                $this->tester->getMollieMockedCreateCaptureResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
