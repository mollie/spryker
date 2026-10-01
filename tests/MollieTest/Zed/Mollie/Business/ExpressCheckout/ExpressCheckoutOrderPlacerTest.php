<?php


declare(strict_types=1);

namespace MollieTest\Zed\Mollie\Business\ExpressCheckout;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer;
use Generated\Shared\Transfer\ShipmentMethodsTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\AddressExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\PaymentExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ShipmentMethodExpander;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutOrderPlacer;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutQuotePreparer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCalculationFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCheckoutFacadeInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToShipmentFacadeInterface;

/**
 * @group MollieTest
 * @group Zed
 * @group Mollie
 * @group Business
 * @group ExpressCheckout
 * @group ExpressCheckoutOrderPlacerTest
 */
class ExpressCheckoutOrderPlacerTest extends Unit
{
    /**
     * @var string
     */
    protected const ORDER_REFERENCE = 'DE--1';

    /**
     * @var int
     */
    protected const GRAND_TOTAL = 4999;

    /**
     * @var int
     */
    protected const ID_SHIPMENT_METHOD = 7;

    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID = '4f1d9a6e-2c5b-4d3a-9e8f-1a2b3c4d5e6f';

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|null
     */
    protected ?QuoteTransfer $placedQuoteTransfer = null;

    /**
     * @return void
     */
    public function testPlaceOrderPreparesQuoteAndReturnsOrderReference(): void
    {
        $placer = $this->createPlacer($this->createSuccessfulCheckoutResponse());

        $response = $placer->placeOrder($this->createRequest(true));

        $this->assertTrue($response->getIsSuccessful());
        $this->assertSame(static::ORDER_REFERENCE, $response->getOrderReference());
        $this->assertSame($this->placedQuoteTransfer, $response->getQuote());

        $quoteTransfer = $this->placedQuoteTransfer;
        $this->assertSame('Request Street 1', $quoteTransfer->getBillingAddress()->getAddress1());
        $this->assertSame('Request Street 1', $quoteTransfer->getItems()[0]->getShipment()->getShippingAddress()->getAddress1());
        $this->assertSame(static::ID_SHIPMENT_METHOD, $quoteTransfer->getItems()[0]->getShipment()->getMethod()->getIdShipmentMethod());
        $this->assertSame(SharedMollieConfig::MOLLIE_PAYMENT_EXPRESS, $quoteTransfer->getPayment()->getPaymentMethod());
        $this->assertSame(SharedMollieConfig::MOLLIE_PROVIDER_EXPRESS, $quoteTransfer->getPayment()->getPaymentProvider());
        $this->assertSame(static::GRAND_TOTAL, $quoteTransfer->getPayment()->getAmount());
        $this->assertSame(static::EXPRESS_CHECKOUT_UUID, $quoteTransfer->getPayment()->getMollieExpressPayment()->getExpressCheckoutUuid());
    }

    /**
     * @return void
     */
    public function testPlaceOrderFallsBackToCustomerAddressWhenRequestHasNone(): void
    {
        $placer = $this->createPlacer($this->createSuccessfulCheckoutResponse());

        $response = $placer->placeOrder($this->createRequest(false));

        $this->assertTrue($response->getIsSuccessful());
        $this->assertSame('Customer Street 9', $this->placedQuoteTransfer->getBillingAddress()->getAddress1());
    }

    /**
     * @return void
     */
    public function testPlaceOrderReturnsCheckoutErrorsWhenCheckoutFails(): void
    {
        $checkoutResponseTransfer = (new CheckoutResponseTransfer())
            ->setIsSuccess(false)
            ->addError((new CheckoutErrorTransfer())->setMessage('Minimum order value not reached.'));

        $response = $this->createPlacer($checkoutResponseTransfer)->placeOrder($this->createRequest(true));

        $this->assertFalse($response->getIsSuccessful());
        $this->assertSame(['Minimum order value not reached.'], $response->getErrors());
    }

    /**
     * @return void
     */
    public function testPlaceOrderFailsWithoutThrowingWhenNoAddressIsAvailable(): void
    {
        $request = $this->createRequest(false);
        $request->getQuote()->setCustomer(null);

        $response = $this->createPlacer($this->createSuccessfulCheckoutResponse())->placeOrder($request);

        $this->assertFalse($response->getIsSuccessful());
        $this->assertNull($this->placedQuoteTransfer);
        $this->assertNotEmpty($response->getErrors());
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Mollie\Zed\Mollie\Business\ExpressCheckout\Order\ExpressCheckoutOrderPlacer
     */
    protected function createPlacer(CheckoutResponseTransfer $checkoutResponseTransfer): ExpressCheckoutOrderPlacer
    {
        $shipmentFacadeMock = $this->createMock(MollieToShipmentFacadeInterface::class);
        $shipmentFacadeMock->method('getAvailableMethodsByShipment')->willReturn(
            (new ShipmentMethodsCollectionTransfer())->addShipmentMethods(
                (new ShipmentMethodsTransfer())->addMethod((new ShipmentMethodTransfer())->setIdShipmentMethod(static::ID_SHIPMENT_METHOD)),
            ),
        );
        $shipmentFacadeMock->method('expandQuoteWithShipmentGroups')->willReturnArgument(0);

        $calculationFacadeMock = $this->createMock(MollieToCalculationFacadeInterface::class);
        $calculationFacadeMock->method('recalculateQuote')->willReturnCallback(
            fn (QuoteTransfer $quoteTransfer) => $quoteTransfer->setTotals((new TotalsTransfer())->setGrandTotal(static::GRAND_TOTAL)),
        );

        $checkoutFacadeMock = $this->createMock(MollieToCheckoutFacadeInterface::class);
        $checkoutFacadeMock->method('placeOrder')->willReturnCallback(
            function (QuoteTransfer $quoteTransfer) use ($checkoutResponseTransfer) {
                $this->placedQuoteTransfer = $quoteTransfer;

                return $checkoutResponseTransfer;
            },
        );

        $quotePreparer = new ExpressCheckoutQuotePreparer(
            [
                new AddressExpander(),
                new ShipmentMethodExpander($shipmentFacadeMock),
                new PaymentExpander(),
            ],
            $calculationFacadeMock,
        );

        return new ExpressCheckoutOrderPlacer($quotePreparer, $checkoutFacadeMock);
    }

    /**
     * @param bool $withAddresses
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer
     */
    protected function createRequest(bool $withAddresses): MollieExpressCheckoutOrderRequestTransfer
    {
        $customerAddressTransfer = $this->createAddress('Customer Street 9');

        $quoteTransfer = (new QuoteTransfer())
            ->addItem(new ItemTransfer())
            ->setCustomer(
                (new CustomerTransfer())
                    ->setBillingAddress(new ArrayObject([$customerAddressTransfer]))
                    ->setShippingAddress(new ArrayObject([$customerAddressTransfer])),
            );

        $request = (new MollieExpressCheckoutOrderRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setExpressCheckoutUuid(static::EXPRESS_CHECKOUT_UUID);

        if ($withAddresses) {
            $request
                ->setBillingAddress($this->createAddress('Request Street 1'))
                ->setShippingAddress($this->createAddress('Request Street 1'));
        }

        return $request;
    }

    /**
     * @param string $address1
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createAddress(string $address1): AddressTransfer
    {
        return (new AddressTransfer())
            ->setAddress1($address1)
            ->setCity('Varazdin')
            ->setZipCode('42000')
            ->setIso2Code('HR');
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function createSuccessfulCheckoutResponse(): CheckoutResponseTransfer
    {
        return (new CheckoutResponseTransfer())
            ->setIsSuccess(true)
            ->setSaveOrder((new SaveOrderTransfer())->setOrderReference(static::ORDER_REFERENCE)->setIdSalesOrder(1));
    }
}
