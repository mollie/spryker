<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Client\Mollie\MollieClientInterface;
use MollieTest\Client\Mollie\AbstractClientTest;

class CreatePaymentApiTest extends AbstractClientTest
{
 /**
  * @return void
  */
    public function testCreatePaymentApi(): void
    {
        $quoteTransfer = new QuoteTransfer();
        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer->setCode('EUR');

        $quoteTransfer
            ->setCurrency($currencyTransfer);

        $mollieCreditCardPaymentTransfer = new MollieCreditCardPaymentTransfer();
        $mollieCreditCardPaymentTransfer->setCardToken('ct_123456789');

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer
            ->setPaymentMethod('mollieCreditCardPayment')
            ->setMollieCreditCardPayment($mollieCreditCardPaymentTransfer)
            ->setAmount(100000);

        $quoteTransfer->setPayment($paymentTransfer);

        $saveOrderTransfer = new SaveOrderTransfer();
        $saveOrderTransfer
            ->setOrderReference('DE-123-123');

        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $checkoutResponseTransfer
            ->setSaveOrder($saveOrderTransfer);

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer
            ->setQuote($quoteTransfer)
            ->setCheckoutResponse($checkoutResponseTransfer);

        $client = $this->createClient();

        $createPaymentResponse = $client->createPayment($mollieApiRequestTransfer);
        $molliePaymentTransfer = $createPaymentResponse->getMolliePayment();

        $this->assertEquals('payment', $molliePaymentTransfer->getResource());
        $this->assertEquals('tr_IUDAHSMGnU6qLbRaksas', $molliePaymentTransfer->getId());
        $this->assertEquals('open', $molliePaymentTransfer->getStatus());
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
            CreatePaymentRequest::class => new MockResponse(
                $this->tester->getMollieMockedPaymentTransactionResponsePayload(),
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
