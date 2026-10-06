<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreatePaymentLinkApiTest extends AbstractClientTest
{
     /**
      * @return void
      */
    public function testCreatePaymentLinkApi(): void
    {
        $paymentLinkTransfer = (new MolliePaymentLinkTransfer())
            ->setDescription('Test payment link')
            ->setRedirectUrl('https://example.com/redirect')
            ->setAmount(1000)
            ->setCurrency('EUR')
            ->setReusable(false)
            ->setExpiresAt('2026-12-31');

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($paymentLinkTransfer);

        $molliePaymentLinkApiResponseTransfer = $this->createClient()->createPaymentLink($mollieApiRequestTransfer);

        $this->assertTrue($molliePaymentLinkApiResponseTransfer->getisSuccessful());
        $this->assertEquals('pl_4Y0eZitmBnQ6IDoMqZQKh', $molliePaymentLinkApiResponseTransfer->getMolliePaymentLink()->getId());
        $this->assertEquals('open', $molliePaymentLinkApiResponseTransfer->getMolliePaymentLink()->getStatus());
    }

    /**
     * @return void
     */
    public function testCreatePaymentLinkApiSendsLinesAndBillingAddress(): void
    {
        $mockMollieClient = $this->createMockApiClientForCreatePaymentRequest();
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);
        $client = $this->createClientMock($mollieFactoryMock);

        $paymentLinkTransfer = $this->createPaymentLinkTransfer();
        $paymentLinkTransfer->addLine($this->createMollieLinesTransfer());
        $paymentLinkTransfer->setBillingAddress($this->createMollieAddressTransfer());

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($paymentLinkTransfer);

        $client->createPaymentLink($mollieApiRequestTransfer);

        $requestBody = [];
        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest) use (&$requestBody): bool {
            $requestBody = $pendingRequest->payload()->all();

            return true;
        });

        $sentAmount = $requestBody['amount'];
        $sentLine = $requestBody['lines'][0];
        $sentBillingAddress = $requestBody['billingAddress'];

        $expectedUnitPrice = ['value' => '107.10', 'currency' => 'EUR'];
        $expectedTotalAmount = ['value' => '214.20', 'currency' => 'EUR'];
        $expectedVatAmount = ['value' => '34.20', 'currency' => 'EUR'];

        $this->assertSame('EUR', $sentAmount['currency']);
        $this->assertSame('214.20', $sentAmount['value']);
        $this->assertSame('Office chair', $sentLine['description']);
        $this->assertSame(2, $sentLine['quantity']);
        $this->assertSame($expectedUnitPrice, $sentLine['unitPrice']);
        $this->assertSame($expectedTotalAmount, $sentLine['totalAmount']);
        $this->assertSame('19.00', $sentLine['vatRate']);
        $this->assertSame($expectedVatAmount, $sentLine['vatAmount']);
        $this->assertSame('John', $sentBillingAddress['givenName']);
        $this->assertSame('Doe', $sentBillingAddress['familyName']);
        $this->assertSame('buyer@example.com', $sentBillingAddress['email']);
        $this->assertSame('DE', $sentBillingAddress['country']);
    }

    /**
     * @return void
     */
    public function testCreatePaymentLinkApiSendsNoLinesAndNoBillingAddressWhenNoneAreSet(): void
    {
        $mockMollieClient = $this->createMockApiClientForCreatePaymentRequest();
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);
        $client = $this->createClientMock($mollieFactoryMock);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setPaymentLink($this->createPaymentLinkTransfer());

        $client->createPaymentLink($mollieApiRequestTransfer);

        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest): bool {
            $requestBody = $pendingRequest->payload()->all();

            return !array_key_exists('lines', $requestBody)
                && !array_key_exists('billingAddress', $requestBody);
        });
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentLinkTransfer
     */
    protected function createPaymentLinkTransfer(): MolliePaymentLinkTransfer
    {
        $paymentLinkTransfer = (new MolliePaymentLinkTransfer())
            ->setDescription('Payment link - Order DE--1')
            ->setAmount(21420)
            ->setCurrency('EUR')
            ->setExpiresAt('2026-12-31');

        return $paymentLinkTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MollieLinesTransfer
     */
    protected function createMollieLinesTransfer(): MollieLinesTransfer
    {
        $unitPriceTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('107.10');

        $totalAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('214.20');

        $vatAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('34.20');

        $mollieLinesTransfer = (new MollieLinesTransfer())
            ->setType('physical')
            ->setDescription('Office chair')
            ->setSku('chair-001')
            ->setQuantity(2)
            ->setUnitPrice($unitPriceTransfer)
            ->setTotalAmount($totalAmountTransfer)
            ->setVatRate('19.00')
            ->setVatAmount($vatAmountTransfer);

        return $mollieLinesTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MollieAddressTransfer
     */
    protected function createMollieAddressTransfer(): MollieAddressTransfer
    {
        $mollieAddressTransfer = (new MollieAddressTransfer())
            ->setGivenName('John')
            ->setFamilyName('Doe')
            ->setStreetAndNumber('Julie-Wolfthorn-Straße 1')
            ->setPostalCode('10115')
            ->setCity('Berlin')
            ->setCountry('DE')
            ->setEmail('buyer@example.com');

        return $mollieAddressTransfer;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    protected function createClient(): MollieClientInterface
    {
         $mollieFactoryMock = $this->createMollieFactoryMock();
         $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForCreatePaymentRequest());

         return $this->createClientMock($mollieFactoryMock);
    }

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClientForCreatePaymentRequest(): MockMollieClient
    {
        $response = [
            CreatePaymentLinkRequest::class => new MockResponse(
                $this->tester->getMollieMockedCreatePaymentLinkResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
