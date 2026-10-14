<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConstants;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreateCustomerApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const MOLLIE_CUSTOMER_ID = 'cst_8wmqcHMN4U';

    /**
     * @var int
     */
    protected const ID_CUSTOMER = 42;

    /**
     * @return void
     */
    public function testCreateCustomerSendsCustomerDataAndReturnsMollieCustomerId(): void
    {
        $mockMollieClient = $this->createMockApiClientForCreateCustomerRequest(new MockResponse([
            'resource' => 'customer',
            'id' => static::MOLLIE_CUSTOMER_ID,
            'mode' => 'test',
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
            'metadata' => [MollieConstants::MOLLIE_CUSTOMER_METADATA_ID_CUSTOMER => static::ID_CUSTOMER],
            'createdAt' => '2026-10-12T10:00:00+00:00',
        ], 201));

        $mollieCustomerApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->createCustomer($this->createMollieApiRequestTransfer());

        $this->assertTrue($mollieCustomerApiResponseTransfer->getIsSuccessful());
        $this->assertSame(static::MOLLIE_CUSTOMER_ID, $mollieCustomerApiResponseTransfer->getMollieCustomer()->getMollieCustomerId());
        $this->assertSame('test', $mollieCustomerApiResponseTransfer->getMollieCustomer()->getMode());
        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            $requestBody = $pendingRequest->getRequest()->payload()->all();

            return $requestBody['name'] === 'John Doe'
                && $requestBody['email'] === 'john.doe@email.com'
                && $requestBody['metadata'][MollieConstants::MOLLIE_CUSTOMER_METADATA_ID_CUSTOMER] === static::ID_CUSTOMER;
        });
    }

    /**
     * @return void
     */
    public function testCreateCustomerReturnsUnsuccessfulResponseWithCodeWhenMollieFails(): void
    {
        $mockMollieClient = $this->createMockApiClientForCreateCustomerRequest(new MockResponse([
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => 'The email address is invalid',
        ], 422));

        $mollieCustomerApiResponseTransfer = $this->createClientWithApiClient($mockMollieClient)
            ->createCustomer($this->createMollieApiRequestTransfer());

        $this->assertFalse($mollieCustomerApiResponseTransfer->getIsSuccessful());
        $this->assertSame(422, $mollieCustomerApiResponseTransfer->getCode());
        $this->assertNull($mollieCustomerApiResponseTransfer->getMollieCustomer());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createMollieApiRequestTransfer(): MollieApiRequestTransfer
    {
        $customerTransfer = (new CustomerTransfer())
            ->setIdCustomer(static::ID_CUSTOMER)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john.doe@email.com');

        return (new MollieApiRequestTransfer())
            ->setQuote((new QuoteTransfer())->setCustomer($customerTransfer));
    }

    /**
     * @param \Mollie\Api\Fake\MockResponse $mockResponse
     *
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    protected function createMockApiClientForCreateCustomerRequest(MockResponse $mockResponse): MockMollieClient
    {
        return $this->createMockApiClient([CreateCustomerRequest::class => $mockResponse]);
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
