<?php

declare(strict_types = 1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApplePayDirectPaymentTransfer;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MollieLogApiTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Client\Mollie\Logger\MollieLoggerInterface;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Client\Mollie\MollieDependencyProvider;
use Mollie\Client\Mollie\MollieFactory;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
use MollieTest\Client\Mollie\AbstractClientTest;
use Spryker\Client\Kernel\Container;

class CreatePaymentApiTest extends AbstractClientTest
{
    /**
     * @var string
     */
    protected const APPLE_PAY_PAYMENT_TOKEN = '{"paymentData":{"version":"EC_v1","data":"vK3BbrCbI"}}';

    /**
     * @var string
     */
    protected const MASKED_VALUE = '***';

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

        $addressTransfer = new AddressTransfer();
        $addressTransfer
            ->setSalutation('Mr.')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setCompany('Company 1')
            ->setAddress1('Street 123')
            ->setZipCode('12345')
            ->setEmail('john.doe@email.com')
            ->setPhone('+38523456789')
            ->setCity('Amsterdam')
            ->setIso2Code('NL');
        $quoteTransfer->setBillingAddress($addressTransfer);

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
     * @return void
     */
    public function testCreatePaymentForApplePayDirectSendsTokenWithCreditCardMethodAndAutomaticCapture(): void
    {
        $mockMollieClient = $this->createMockApiClientForCreatePaymentRequest();
        $mollieFactoryMock = $this->createMollieFactoryMock();
        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($mockMollieClient);
        $client = $this->createClientMock($mollieFactoryMock);

        $automaticCaptureMode = (new MollieConfig())->getMollieAutomaticCaptureMode();

        $client->createPayment($this->createApplePayDirectMollieApiRequestTransfer());

        $mockMollieClient->assertSent(function (PendingRequest $pendingRequest) use ($automaticCaptureMode): bool {
            $requestBody = $pendingRequest->getRequest()->payload()->all();

            return $requestBody['method'] === PaymentMethod::CREDITCARD
                && $requestBody['applePayPaymentToken'] === static::APPLE_PAY_PAYMENT_TOKEN
                && $requestBody['captureMode'] === $automaticCaptureMode;
        });
    }

    /**
     * @return void
     */
    public function testCreatePaymentForApplePayDirectMasksTokenInLoggedRequestBody(): void
    {
        $mollieLoggerMock = $this->createMock(MollieLoggerInterface::class);
        $mollieLoggerMock->expects($this->once())
            ->method('logResponse')
            ->with($this->callback(function (MollieLogApiTransfer $mollieLogApiTransfer): bool {
                $requestBody = $mollieLogApiTransfer->getRequestBody();

                return $requestBody['applePayPaymentToken'] === static::MASKED_VALUE;
            }));
        $client = $this->createClientMock($this->createMollieFactoryMockWithLogger($mollieLoggerMock));

        $client->createPayment($this->createApplePayDirectMollieApiRequestTransfer());
    }

    /**
     * @return \Generated\Shared\Transfer\MollieApiRequestTransfer
     */
    protected function createApplePayDirectMollieApiRequestTransfer(): MollieApiRequestTransfer
    {
        $currencyTransfer = (new CurrencyTransfer())->setCode('EUR');
        $mollieApplePayDirectPaymentTransfer = (new MollieApplePayDirectPaymentTransfer())
            ->setApplePayPaymentToken(static::APPLE_PAY_PAYMENT_TOKEN);

        $paymentTransfer = (new PaymentTransfer())
            ->setPaymentMethod(SharedMollieConfig::MOLLIE_PAYMENT_APPLE_PAY_DIRECT)
            ->setMollieApplePayDirectPayment($mollieApplePayDirectPaymentTransfer)
            ->setAmount(100000);

        $addressTransfer = (new AddressTransfer())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setAddress1('Street 123')
            ->setZipCode('12345')
            ->setEmail('john.doe@email.com')
            ->setCity('Amsterdam')
            ->setIso2Code('NL');

        $quoteTransfer = (new QuoteTransfer())
            ->setCurrency($currencyTransfer)
            ->setPayment($paymentTransfer)
            ->setBillingAddress($addressTransfer);

        $checkoutResponseTransfer = (new CheckoutResponseTransfer())
            ->setSaveOrder((new SaveOrderTransfer())->setOrderReference('DE-123-123'));

        return (new MollieApiRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setCheckoutResponse($checkoutResponseTransfer);
    }

    /**
     * @param \Mollie\Client\Mollie\Logger\MollieLoggerInterface $mollieLogger
     *
     * @return \Mollie\Client\Mollie\MollieFactory
     */
    protected function createMollieFactoryMockWithLogger(MollieLoggerInterface $mollieLogger): MollieFactory
    {
        $mollieFactoryMock = $this->getMockBuilder(MollieFactory::class)
            ->onlyMethods(['createMollieApiClient', 'getStorageClient', 'createMollieLogger'])
            ->getMock();

        $mollieFactoryMock->setConfig(new MollieConfig());
        $container = new Container();
        (new MollieDependencyProvider())->provideServiceLayerDependencies($container);
        $mollieFactoryMock->setContainer($container);

        $mollieFactoryMock->method('createMollieApiClient')
            ->willReturn($this->createMockApiClientForCreatePaymentRequest());
        $mollieFactoryMock->method('createMollieLogger')
            ->willReturn($mollieLogger);

        return $mollieFactoryMock;
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
