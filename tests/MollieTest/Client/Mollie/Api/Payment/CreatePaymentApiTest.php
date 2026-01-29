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
            ->setPaymentMethod('creditcard')
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
                '{"resource":"payment","id":"tr_IUDAHSMGnU6qLbRaksas","mode":"live","amount":{"value":"10.00","currency":"EUR"},"description":"Order #12345","sequenceType":"oneoff","redirectUrl":"https://webshop.example.org/order/12345/","webhookUrl":"https://webshop.example.org/payments/webhook/","metadata":{"order_id":12345},"profileId":"pfl_QkEhN94Ba","status":"open","isCancelable":false,"createdAt":"2024-03-20T09:13:37+00:00","expiresAt":"2024-03-20T09:28:37+00:00","_links":{"self":{"href":"...","type":"application/hal+json"},"checkout":{"href":"https://www.mollie.com/checkout/select-method/7UhSN1zuXS","type":"text/html"},"dashboard":{"href":"https://www.mollie.com/dashboard/org_12345678/payments/tr_5B8cwPMGnU6qLbRvo7qEZo","type":"text/html"},"documentation":{"href":"...","type":"text/html"}}}',
            ),
        ];

        return $this->createMockApiClient($response);
    }
}
