<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Wallet;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreateApplePayPaymentSessionApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const APPLE_PAY_DOMAIN = 'shop.example.com';

    /**
     * @var string
     */
    protected const APPLE_PAY_VALIDATION_URL = 'https://apple-pay-gateway.apple.com/paymentservices/startSession';

    /**
     * @var array<string, mixed>
     */
    protected const APPLE_PAY_PAYMENT_SESSION = [
        'epochTimestamp' => 1555507990353,
        'expiresAt' => 1555511590353,
        'merchantSessionIdentifier' => 'SSH2EAF8AFAEAA94DEEA898162A5C6C2340_916523BAF0B1FA2D2B0C2C6F2E9D45FDC1E4F5A1',
        'nonce' => '0206b8db',
        'merchantIdentifier' => 'BD62FEB196874511C22DB28A9E14A89E3534C93194F73EA417EC566368D391EB',
        'domainName' => 'shop.example.com',
        'displayName' => 'Mollie Shop',
        'signature' => '308006092a864886f70d010702a0803080020101310f300d06096086480165030402010500',
    ];

    /**
     * @return void
     */
    public function testCreateApplePayPaymentSessionReturnsApplePayPaymentSessionUnchanged(): void
    {
        $mockMollieClient = $this->createMockApiClient([
            ApplePayPaymentSessionRequest::class => new MockResponse(static::APPLE_PAY_PAYMENT_SESSION, 201),
        ]);
        $client = $this->createClient($mockMollieClient);

        $mollieApplePayPaymentSessionApiResponseTransfer = $client->createApplePayPaymentSession($this->createMollieApiRequestTransfer());

        $this->assertTrue($mollieApplePayPaymentSessionApiResponseTransfer->getIsSuccessful());
        $this->assertSame(static::APPLE_PAY_PAYMENT_SESSION, $mollieApplePayPaymentSessionApiResponseTransfer->getApplePayPaymentSession());
    }

    /**
     * @return void
     */
    public function testCreateApplePayPaymentSessionSendsDomainAndValidationUrlToMollie(): void
    {
        $mockMollieClient = $this->createMockApiClient([
            ApplePayPaymentSessionRequest::class => new MockResponse(static::APPLE_PAY_PAYMENT_SESSION, 201),
        ]);
        $client = $this->createClient($mockMollieClient);

        $client->createApplePayPaymentSession($this->createMollieApiRequestTransfer());

        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            $requestBody = $pendingRequest->getRequest()->payload()->all();

            return $requestBody['domain'] === static::APPLE_PAY_DOMAIN
                && $requestBody['validationUrl'] === static::APPLE_PAY_VALIDATION_URL;
        });
    }

    /**
     * @return void
     */
    public function testCreateApplePayPaymentSessionReturnsUnsuccessfulResponseWhenMollieRejectsRequest(): void
    {
        $mockMollieClient = $this->createMockApiClient([
            ApplePayPaymentSessionRequest::class => new MockResponse([
                'status' => 422,
                'title' => 'Unprocessable Entity',
                'detail' => 'Domain verification failed',
            ], 422),
        ]);
        $client = $this->createClient($mockMollieClient);

        $mollieApplePayPaymentSessionApiResponseTransfer = $client->createApplePayPaymentSession($this->createMollieApiRequestTransfer());

        $this->assertFalse($mollieApplePayPaymentSessionApiResponseTransfer->getIsSuccessful());
        $this->assertEmpty($mollieApplePayPaymentSessionApiResponseTransfer->getApplePayPaymentSession());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createMollieApiRequestTransfer(): MollieApiRequestTransfer
    {
        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer
            ->setApplePayDomain(static::APPLE_PAY_DOMAIN)
            ->setApplePayValidationUrl(static::APPLE_PAY_VALIDATION_URL);

        return $mollieApiRequestTransfer;
    }

    /**
     * @param \Mollie\Api\Fake\MockMollieClient $mockMollieClient
     *
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(MockMollieClient $mockMollieClient): MollieClientInterface
    {
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);

        return $this->createClientMock($mollieFactoryMock);
    }
}
