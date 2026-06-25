<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
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
     * @return void
     */
    public function testCreateRefundWithIdempotencyKeySetsKeyOnClient(): void
    {
        $mollieAmount = (new MollieAmountTransfer())
            ->setValue('307.85')
            ->setCurrency('EUR');

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ')
            ->setAmount($mollieAmount)
            ->setDescription('DE--341657-131173-6871')
            ->setMetadata(['{"orderReference"' => '"DE--341657-131173-6871"}']);

        $idempotencyKey = '550e8400-e29b-41d4-a716-446655440000';
        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefund($mollieRefundTransfer)
            ->setIdempotencyKey($idempotencyKey);

        $mockClient = $this->createSpyClient();
        $mockClient->expects($this->once())
            ->method('setIdempotencyKey')
            ->with($idempotencyKey)
            ->willReturnSelf();

        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockClient);
        $client = $this->createClientMock($mollieFactoryMock);

        $createRefundResponse = $client->createRefund($mollieApiRequestTransfer);

        $this->assertTrue($createRefundResponse->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testCreateRefundWithoutIdempotencyKeyDoesNotSetKey(): void
    {
        $mollieAmount = (new MollieAmountTransfer())
            ->setValue('307.85')
            ->setCurrency('EUR');

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setTransactionId('tr_7FQgLEW7ECECKWStSwTLJ')
            ->setAmount($mollieAmount)
            ->setDescription('DE--341657-131173-6871')
            ->setMetadata(['{"orderReference"' => '"DE--341657-131173-6871"}']);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefund($mollieRefundTransfer);

        $mockClient = $this->createSpyClient();
        $mockClient->expects($this->never())
            ->method('setIdempotencyKey');

        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockClient);
        $client = $this->createClientMock($mollieFactoryMock);

        $createRefundResponse = $client->createRefund($mollieApiRequestTransfer);

        $this->assertTrue($createRefundResponse->getIsSuccessful());
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

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createSpyClient(): MockMollieClient
    {
        $response = [
            CreatePaymentRefundRequest::class => new MockResponse(
                $this->tester->getMollieMockedRefundTransactionResponsePayload(),
            ),
        ];

        return $this->getMockBuilder(MockMollieClient::class)
            ->setConstructorArgs([$response])
            ->onlyMethods(['setIdempotencyKey'])
            ->getMock();
    }
}
