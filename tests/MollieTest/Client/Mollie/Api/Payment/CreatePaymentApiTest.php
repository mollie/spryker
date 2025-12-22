<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Api\Payment;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieCreditCardPaymentTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Client\Mollie\Api\ApiCallInterface;
use Mollie\Client\Mollie\Api\Payment\CreatePaymentApi;
use Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface;
use Mollie\Client\Mollie\Mapper\Payment\PaymentMapper;
use Mollie\Client\Mollie\MollieClient;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Client\Mollie\MollieFactory;
use Mollie\Client\Mollie\Storage\MolliePaymentStorageSaver;
use Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface;
use MollieTest\Client\Mollie\MollieApiClientTester;

class CreatePaymentApiTest extends Unit
{
    protected MollieApiClientTester $tester;

    /**
     * @return \Mollie\Api\Fake\MockMollieClient
     */
    public function createMockApiClient(): MockMollieClient
    {
        $client = MollieApiClient::fake([
            CreatePaymentRequest::class => new MockResponse(
                $this->tester->getMollieMockedCreatePaymentResponsePayload(),
            ),
        ]);

        $client->setApiKey('test_jdkshfdsk1213sjkadsdasfdsafdsfdsfdsfds2qeqasx');

        return $client;
    }

    /**
     * @return \Mollie\Client\Mollie\MollieClientInterface
     */
    public function createMollieClient(): MollieClientInterface
    {
        return (new MollieClient())
            ->setFactory($this->createFactory());
    }

    /**
     * @return \Mollie\Client\Mollie\MollieFactory
     */
    protected function createFactory(): MollieFactory
    {
        return $this->tester->mockFactoryMethod('createCreatePaymentApi', $this->createCreatePaymentApiMock());
    }

    /**
     * @return \Mollie\Client\Mollie\Mapper\MollieApiResponseMapperInterface
     */
    protected function createPaymentMapper(): MollieApiResponseMapperInterface
    {
        return $this->getMockBuilder(PaymentMapper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Mollie\Client\Mollie\Storage\MolliePaymentStorageSaverInterface
     */
    protected function createStorageSaver(): MolliePaymentStorageSaverInterface
    {
        return $this->getMockBuilder(MolliePaymentStorageSaver::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Mollie\Client\Mollie\MollieConfig
     */
    protected function createMollieConfig(): MollieConfig
    {
        return $this->getMockBuilder(MollieConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Mollie\Client\Mollie\Api\ApiCallInterface
     */
    public function createCreatePaymentApiMock(): ApiCallInterface
    {
        $client = $this->createMockApiClient();
        $paymentMapper = $this->createPaymentMapper();
        $storageSaver = $this->createStorageSaver();
        $config = $this->createMollieConfig();
        $stub = new CreatePaymentApi($client, $paymentMapper, $storageSaver, $config);

        return $stub;
    }

    /**
     * @return void
     */
    public function testCreatePaymentApi(): void
    {
        $client = $this->createMollieClient();

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer
            ->setCurrency('EUR');

        $mollieCreditCardPaymentTransfer = new MollieCreditCardPaymentTransfer();
        $mollieCreditCardPaymentTransfer->setCardToken('ct_123456789');

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer
            ->setPaymentMethod('creditcard')
            ->setMollieCreditCardPayment($mollieCreditCardPaymentTransfer)
            ->setAmount(100000);

        $orderTransfer = new OrderTransfer();
        $orderTransfer
            ->setOrderReference('DE-123-123')
            ->setPayment($paymentTransfer);

        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $checkoutResponseTransfer
            ->setSaveOrder($orderTransfer);

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer
            ->setQuote($quoteTransfer)
            ->setCheckoutResponse($checkoutResponseTransfer);

        $createPaymentResponse = $client->createPayment($mollieApiRequestTransfer);

        $this->assertNotEmpty($createPaymentResponse);
        $this->assertInstanceOf(MolliePaymentTransfer::class, $createPaymentResponse);
    }
}
