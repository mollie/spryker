<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class DeleteCustomerApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @return void
     */
    public function testDeleteCustomerIsSuccessfulWhenMollieReturnsNoContent(): void
    {
        $mockMollieClient = $this->createMockApiClient([DeleteCustomerRequest::class => MockResponse::noContent()]);

        $mollieCustomerApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->deleteCustomer((new MollieApiRequestTransfer())->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID));

        $this->assertTrue($mollieCustomerApiResponseTransfer->getIsSuccessful());
        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            return $pendingRequest->getRequest()->resolveResourcePath() === sprintf('customers/%s', static::MOLLIE_CUSTOMER_ID);
        });
    }

    /**
     * @return void
     */
    public function testDeleteCustomerReturnsCodeWhenCustomerIsUnknown(): void
    {
        $notFoundResponse = new MockResponse([
            'status' => 404,
            'title' => 'Not Found',
            'detail' => 'No customer exists with token cst_8wmqcHMN4U.',
        ], 404);
        $mockMollieClient = $this->createMockApiClient([DeleteCustomerRequest::class => $notFoundResponse]);

        $mollieCustomerApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->deleteCustomer((new MollieApiRequestTransfer())->setMollieCustomerId(static::MOLLIE_CUSTOMER_ID));

        $this->assertFalse($mollieCustomerApiResponseTransfer->getIsSuccessful());
        $this->assertSame(404, $mollieCustomerApiResponseTransfer->getCode());
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
