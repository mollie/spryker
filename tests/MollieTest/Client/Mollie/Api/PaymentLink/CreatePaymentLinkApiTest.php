<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\PaymentLink;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
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
        $amountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('10.00');

        $paymentLinkTransfer = (new MolliePaymentLinkTransfer())
            ->setDescription('Test payment link')
            ->setRedirectUrl('https://example.com/redirect')
            ->setAmount($amountTransfer)
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
