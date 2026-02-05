<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreateRefundApiTest extends AbstractClientTest
{
    /**
     * @return void
     */
    public function testCreateRefundApi(): void
    {
        $mollieAmount = (new MollieAmountTransfer())
            ->setValue('307.85')
            ->setCurrency('EUR');

        $molliePaymentMethodQueryParameters = (new MolliePaymentMethodQueryParametersTransfer())
            ->setAmount($mollieAmount);

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ')
            ->setAmount($mollieAmount)
            ->setDescription('DE--341657-131173-6871')
            ->setMetadata(['{"orderReference"' => '"DE--341657-131173-6871"}']);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefund($mollieRefundTransfer);

        $client = $this->createClient();

        $createRefundResponse = $client->createRefund($mollieApiRequestTransfer);
        $mollieRefundTransfer = $createRefundResponse->getMollieRefund();

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
            ->willReturn($this->createMockApiClientForCreateRefundRequest());

        return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForCreateRefundRequest(): MockMollieClient
    {
        $response = [
            CreatePaymentRefundRequest::class => new MockResponse(
                $this->tester->getMollieMockedRefundTransactionResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
