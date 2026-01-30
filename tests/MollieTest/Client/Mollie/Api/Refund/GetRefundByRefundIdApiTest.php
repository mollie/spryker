<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\Refund;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class GetRefundByRefundIdApiTest extends AbstractClientTest
{
    /**
     * @return void
     */
    public function testGetRefundByRefundIdApi(): void
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ');
        $mollieApiRequestTransfer->setRefundId('re_yuj7TaDpm877xZQzP8ULJ');

        $client = $this->createClient();

        $mollieRefundApiResponseTransfer = $client->getRefundByRefundId($mollieApiRequestTransfer);
        $mollieRefundTransfer = $mollieRefundApiResponseTransfer->getMollieRefund();

        $this->assertEquals('refund', $mollieRefundTransfer->getResource());
        $this->assertEquals('re_yuj7TaDpm877xZQzP8ULJ', $mollieRefundTransfer->getId());
        $this->assertEquals('pending', $mollieRefundTransfer->getStatus());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForGetRefundRequest());

        return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForGetRefundRequest(): MockMollieClient
    {
        $response = [
            GetPaymentRefundRequest::class => new MockResponse(
                $this->tester->getMollieMockedRefundTransactionResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
