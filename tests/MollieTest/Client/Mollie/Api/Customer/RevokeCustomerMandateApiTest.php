<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class RevokeCustomerMandateApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @var string
     */
    protected const MANDATE_ID = 'mdt_pWUnw6pkBN';

    /**
     * @return void
     */
    public function testRevokeCustomerMandateIsSuccessfulWhenMollieReturnsNoContent(): void
    {
        $mockMollieClient = $this->createMockApiClientForRevokeRequest(MockResponse::noContent());

        $mollieMandateApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->revokeCustomerMandate($this->createMollieApiRequestTransfer());

        $this->assertTrue($mollieMandateApiResponseTransfer->getIsSuccessful());
        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            return $pendingRequest->getRequest()->resolveResourcePath()
                === sprintf('customers/%s/mandates/%s', static::MOLLIE_CUSTOMER_ID, static::MANDATE_ID);
        });
    }

    /**
     * @return void
     */
    public function testRevokeCustomerMandateReturnsCodeWhenMandateIsUnknown(): void
    {
        $mockMollieClient = $this->createMockApiClientForRevokeRequest(new MockResponse([
            'status' => 404,
            'title' => 'Not Found',
            'detail' => 'No mandate exists with token mdt_pWUnw6pkBN.',
        ], 404));

        $mollieMandateApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->revokeCustomerMandate($this->createMollieApiRequestTransfer());

        $this->assertFalse($mollieMandateApiResponseTransfer->getIsSuccessful());
        $this->assertSame(404, $mollieMandateApiResponseTransfer->getCode());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createMollieApiRequestTransfer(): MollieApiRequestTransfer
    {
        return (new MollieApiRequestTransfer())
            ->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID)
            ->setMandateId(static::MANDATE_ID);
    }

    /**
     * @param \Mollie\Api\Fake\MockResponse $mockResponse
     *
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    protected function createMockApiClientForRevokeRequest(MockResponse $mockResponse): MockMollieClient
    {
        return $this->createMockApiClient([RevokeMandateRequest::class => $mockResponse]);
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
