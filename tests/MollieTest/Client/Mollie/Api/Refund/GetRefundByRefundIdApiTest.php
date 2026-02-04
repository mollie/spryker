<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\Refund;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
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
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setId('re_yuj7TaDpm877xZQzP8ULJ')
            ->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ');

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefund($mollieRefundTransfer);

        $client = $this->createClient();

        $mollieRefundApiResponseTransfer = $client->getRefundByRefundId($mollieApiRequestTransfer);
        $mollieRefundTransfer = $mollieRefundApiResponseTransfer->getMollieRefund();

        $this->assertEquals('refund', $mollieRefundTransfer->getResource());
        $this->assertEquals('re_yuj7TaDpm877xZQzP8ULJ', $mollieRefundTransfer->getId());
        $this->assertEquals('refunded', $mollieRefundTransfer->getStatus());
        $this->assertEquals('307.85', $mollieRefundTransfer->getAmount()->getValue());
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
