<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class GetCustomerMandatesApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @return void
     */
    public function testGetCustomerMandatesReturnsOnlyValidCreditCardMandates(): void
    {
        $mockMollieClient = $this->createMockApiClientForMandatesRequest(new MockResponse([
            'count' => 3,
            '_embedded' => [
                'mandates' => [
                    $this->createMandatePayload('mdt_valid_card', 'valid', 'creditcard'),
                    $this->createMandatePayload('mdt_invalid_card', 'invalid', 'creditcard'),
                    $this->createMandatePayload('mdt_valid_directdebit', 'valid', 'directdebit'),
                ],
            ],
            '_links' => [
                'self' => ['href' => 'https://api.mollie.com/v2/customers/cst_8wmqcHMN4U/mandates', 'type' => 'application/hal+json'],
            ],
        ]));

        $mollieMandateApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->getCustomerMandates((new MollieApiRequestTransfer())->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID));

        $mandates = $mollieMandateApiResponseTransfer->getMandateCollection()->getMandates();
        $this->assertTrue($mollieMandateApiResponseTransfer->getIsSuccessful());
        $this->assertCount(1, $mandates);
        $this->assertSame('mdt_valid_card', $mandates[0]->getId());
        $this->assertSame('John Doe', $mandates[0]->getCardHolder());
        $this->assertSame('4242', $mandates[0]->getCardNumberLast4());
        $this->assertSame('Visa', $mandates[0]->getCardLabel());
        $this->assertSame('2027-12-31', $mandates[0]->getCardExpiryDate());
        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            return str_contains($pendingRequest->getRequest()->resolveResourcePath(), static::MOLLIE_CUSTOMER_ID);
        });
    }

    /**
     * @return void
     */
    public function testGetCustomerMandatesReturnsCodeWhenCustomerIsGone(): void
    {
        $mockMollieClient = $this->createMockApiClientForMandatesRequest(new MockResponse([
            'status' => 404,
            'title' => 'Not Found',
            'detail' => 'No customer exists with token cst_8wmqcHMN4U.',
        ], 404));

        $mollieMandateApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->getCustomerMandates((new MollieApiRequestTransfer())->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID));

        $this->assertFalse($mollieMandateApiResponseTransfer->getIsSuccessful());
        $this->assertSame(404, $mollieMandateApiResponseTransfer->getCode());
        $this->assertCount(0, $mollieMandateApiResponseTransfer->getMandateCollection()->getMandates());
    }

    /**
     * @param string $id
     * @param string $status
     * @param string $method
     *
     * @return array<string, mixed>
     */
    protected function createMandatePayload(string $id, string $status, string $method): array
    {
        return [
            'resource' => 'mandate',
            'id' => $id,
            'mode' => 'test',
            'status' => $status,
            'method' => $method,
            'details' => [
                'cardHolder' => 'John Doe',
                'cardNumber' => '4242',
                'cardLabel' => 'Visa',
                'cardExpiryDate' => '2027-12-31',
            ],
            'createdAt' => '2026-10-12T10:00:00+00:00',
        ];
    }

    /**
     * @param \Mollie\Api\Fake\MockResponse $mockResponse
     *
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    protected function createMockApiClientForMandatesRequest(MockResponse $mockResponse): MockMollieClient
    {
        return $this->createMockApiClient([GetPaginatedMandateRequest::class => $mockResponse]);
    }

    /**
     * @param \Mollie\Api\Fake\MockMollieClient $mockMollieClient
     *
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClientWithApiClient(MockMollieClient $mockMollieClient): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);

        return $this->createClientMock($mollieFactoryMock);
    }
}
