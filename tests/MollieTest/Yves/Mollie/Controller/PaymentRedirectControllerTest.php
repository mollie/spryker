<?php

namespace MollieTest\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Yves\Mollie\Controller\PaymentRedirectController;
use Mollie\Yves\Mollie\MollieConfig;
use Mollie\Yves\Mollie\MollieFactory;
use MollieTest\Yves\Mollie\AbstractYvesTest;
use PHPUnit\Framework\MockObject\MockObject;
use SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentRedirectControllerTest extends AbstractYvesTest
{
    protected MollieConfig $mollieConfig;

    protected PaymentRedirectController $paymentRedirectController;

    protected MollieFactory|MockObject $factory;

    /**
     * @var string
     */
    protected const ORDER_REFERENCE = 'TEST--ORDER-123';

    /**
     * @var string
     */
    protected const PAYMENT_ID = 'tr_123123';

    /**
     * @var string
     */
    protected const STORAGE_KEY = 'mollie:payment:' . self::ORDER_REFERENCE;

    /**
     * @var string
     */
    protected const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->storageClient = $this->createMollieToStorageClientBridgeMock();
        $this->mollieClient = $this->createMollieClientMock();
        $this->quoteClient = $this->createMollieToQuoteClientMock();
        $this->mollieConfig = new MollieConfig();
        $this->factory = $this->createFactoryMock();
        $this->paymentRedirectController = $this->createPaymentRedirectController();
    }

    /**
     * @return void
     */
    public function testPaymentRedirectActionHandlesFailedPaymentStatuses(): void
    {
        $request = $this->createRequestWithOrderReference(static::ORDER_REFERENCE);
        $this->storageClient
            ->expects($this->once())
            ->method('get')
            ->with(static::STORAGE_KEY)
            ->willReturn(static::PAYMENT_ID);

        $paymentStatus = static::PAYMENT_STATUS_FAILED;

        $molliePaymentApiResponse = $this->createMolliePaymentApiResponse(true, $paymentStatus);

        $this->mollieClient
            ->method('getPaymentByTransactionId')
            ->willReturn($molliePaymentApiResponse);

        $this->expectQuoteToBeClearedOnce();

        $response = $this->paymentRedirectController->paymentRedirectAction($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(
            '/checkout/payment',
            $response->getTargetUrl(),
        );
    }

    /**
     * @param string $orderReference
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequestWithOrderReference(string $orderReference): Request
    {
        return new Request(['orderReference' => $orderReference]);
    }

    /**
     * @param bool $isSuccessful
     * @param string $status
     * @param string|null $message
     *
     * @return \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer
     */
    protected function createMolliePaymentApiResponse(bool $isSuccessful, string $status, ?string $message = null): MolliePaymentApiResponseTransfer
    {
        $molliePaymentTransfer = (new MolliePaymentTransfer())
            ->setStatus($status)
            ->setId(static::PAYMENT_ID);

        $response = (new MolliePaymentApiResponseTransfer())
            ->setMolliePayment($molliePaymentTransfer)
            ->setIsSuccessful($isSuccessful)
            ->setMessage($message);

        return $response;
    }

    /**
     * @return void
     */
    protected function expectQuoteToBeClearedOnce(): void
    {
        $quoteTransfer = new QuoteTransfer();

        $this->quoteClient
            ->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteTransfer);

        $this->quoteClient
            ->expects($this->once())
            ->method('setQuote');
    }

    /**
     * @return \Mollie\Yves\Mollie\Controller\PaymentRedirectController
     */
    protected function createPaymentRedirectController(): PaymentRedirectController
    {
        $this->paymentRedirectController = $this->getMockBuilder(PaymentRedirectController::class)
            ->onlyMethods(['getFactory', 'addErrorMessage', 'redirectResponseInternal'])
            ->getMock();

        $this->paymentRedirectController
            ->method('getFactory')
            ->willReturn($this->factory);

        $this->paymentRedirectController
            ->method('redirectResponseInternal')
            ->willReturnCallback(function ($routeName) {
                return new RedirectResponse(
                    $routeName === CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PAYMENT
                        ? '/checkout/payment'
                        : '/checkout/success',
                );
            });

        return $this->paymentRedirectController;
    }

    /**
     * @return \Mollie\Yves\Mollie\MollieFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createFactoryMock(): MollieFactory|MockObject
    {
        $factory = $this->getMockBuilder(MollieFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getConfig',
                'getStorageClient',
                'getQuoteClient',
                'getMollieApiClient',
            ])
            ->getMock();

        $factory->method('getConfig')->willReturn($this->mollieConfig);
        $factory->method('getStorageClient')->willReturn($this->storageClient);
        $factory->method('getQuoteClient')->willReturn($this->quoteClient);
        $factory->method('getMollieApiClient')->willReturn($this->mollieClient);

        return $factory;
    }
}
