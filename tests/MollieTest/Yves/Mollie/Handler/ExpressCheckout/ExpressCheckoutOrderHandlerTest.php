<?php

declare(strict_types=1);

namespace MollieTest\Yves\Mollie\Handler\ExpressCheckout;

use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Service\Mollie\MollieService;
use Mollie\Yves\Mollie\Handler\ExpressCheckout\ExpressCheckoutOrderHandlerInterface;
use Mollie\Yves\Mollie\MollieFactory;
use MollieTest\Yves\Mollie\AbstractYvesTest;

/**
 * @group MollieTest
 * @group Yves
 * @group Mollie
 * @group Handler
 * @group ExpressCheckout
 * @group ExpressCheckoutOrderHandlerTest
 */
class ExpressCheckoutOrderHandlerTest extends AbstractYvesTest
{
    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '4f1d9a6e-2c5b-4d3a-9e8f-1a2b3c4d5e6f';

    /**
     * @var string
     */
    protected const MOLLIE_SESSION_ID = 'sess_7behWYUFsjn5hwRB86mXJ';

    /**
     * @var string
     */
    protected const ORDER_REFERENCE = 'DE--1';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mollieClient = $this->createMollieClientMock();
        $this->quoteClient = $this->createMollieToQuoteClientMock();
        $this->quoteClient->method('getQuote')->willReturn(new QuoteTransfer());
    }

    /**
     * @return void
     */
    public function testPlaceOrderStoresPlacedQuoteWhenOrderIsPlaced(): void
    {
        $mollieExpressCheckoutOrderResponseTransfer = (new MollieExpressCheckoutOrderResponseTransfer())
            ->setIsSuccessful(true)
            ->setQuote(new QuoteTransfer())
            ->setOrderReference(static::ORDER_REFERENCE);

        $this->mollieClient->expects($this->once())
            ->method('placeExpressCheckoutOrder')
            ->with($this->callback(function (MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer): bool {
                return $mollieExpressCheckoutOrderRequestTransfer->getExpressCheckoutUuid() === static::EXPRESS_CHECKOUT_UUID
                    && $mollieExpressCheckoutOrderRequestTransfer->getQuote() !== null
                    && $mollieExpressCheckoutOrderRequestTransfer->getExpectedGrandTotal() === 5498;
            }))
            ->willReturn($mollieExpressCheckoutOrderResponseTransfer);
        $this->mollieClient->expects($this->never())->method('createExpressCheckoutPaymentRefund');
        $this->quoteClient->expects($this->once())
            ->method('setQuote')
            ->with($this->callback(function (QuoteTransfer $quoteTransfer): bool {
                return $quoteTransfer->getOrderReference() === static::ORDER_REFERENCE
                    && $quoteTransfer->getCheckoutConfirmed() === true
                    && $quoteTransfer->getIsOrderPlacedSuccessfully() === true;
            }));

        $placedMollieExpressCheckoutOrderResponseTransfer = $this->createExpressCheckoutOrderHandler()
            ->placeOrder($this->createMollieExpressCheckoutSessionTransfer(), static::EXPRESS_CHECKOUT_UUID);

        $this->assertTrue($placedMollieExpressCheckoutOrderResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testPlaceOrderCreatesPaymentRefundWhenOrderIsNotPlaced(): void
    {
        $mollieExpressCheckoutOrderResponseTransfer = (new MollieExpressCheckoutOrderResponseTransfer())
            ->setIsSuccessful(false)
            ->addError('Minimum order value not reached.');

        $this->mollieClient->method('placeExpressCheckoutOrder')->willReturn($mollieExpressCheckoutOrderResponseTransfer);
        $this->mollieClient->expects($this->once())
            ->method('createExpressCheckoutPaymentRefund')
            ->with($this->callback(function (MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer): bool {
                return $mollieExpressCheckoutPaymentRefundTransfer->getExpressCheckoutUuid() === static::EXPRESS_CHECKOUT_UUID
                    && $mollieExpressCheckoutPaymentRefundTransfer->getMollieSessionId() === static::MOLLIE_SESSION_ID;
            }))
            ->willReturnArgument(0);
        $this->quoteClient->expects($this->never())->method('setQuote');

        $notPlacedMollieExpressCheckoutOrderResponseTransfer = $this->createExpressCheckoutOrderHandler()
            ->placeOrder($this->createMollieExpressCheckoutSessionTransfer(), static::EXPRESS_CHECKOUT_UUID);

        $this->assertFalse($notPlacedMollieExpressCheckoutOrderResponseTransfer->getIsSuccessful());
    }

    /**
     * @return \Mollie\Yves\Mollie\Handler\ExpressCheckout\ExpressCheckoutOrderHandlerInterface
     */
    protected function createExpressCheckoutOrderHandler(): ExpressCheckoutOrderHandlerInterface
    {
        $factoryMock = $this->getMockBuilder(MollieFactory::class)
            ->onlyMethods(['getMollieApiClient', 'getQuoteClient', 'getMollieService'])
            ->getMock();
        $factoryMock->method('getMollieApiClient')->willReturn($this->mollieClient);
        $factoryMock->method('getQuoteClient')->willReturn($this->quoteClient);
        $factoryMock->method('getMollieService')->willReturn(new MollieService());

        return $factoryMock->createExpressCheckoutOrderHandler();
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer
     */
    protected function createMollieExpressCheckoutSessionTransfer(): MollieExpressCheckoutSessionTransfer
    {
        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('54.98');
        $mollieAddressTransfer = (new MollieAddressTransfer())
            ->setStreetAndNumber('Billing Street 1')
            ->setCity('Varazdin')
            ->setCountry('HR');

        return (new MollieExpressCheckoutSessionTransfer())
            ->setId(static::MOLLIE_SESSION_ID)
            ->setAmount($mollieAmountTransfer)
            ->setBillingAddress($mollieAddressTransfer);
    }
}
