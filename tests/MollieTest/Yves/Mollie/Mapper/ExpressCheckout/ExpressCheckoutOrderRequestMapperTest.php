<?php

declare(strict_types=1);

namespace MollieTest\Yves\Mollie\Mapper\ExpressCheckout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MollieAddressTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Generated\Shared\Transfer\MollieLinesTransfer;
use Mollie\Service\Mollie\MollieService;
use Mollie\Yves\Mollie\Mapper\ExpressCheckout\ExpressCheckoutOrderRequestMapperInterface;
use Mollie\Yves\Mollie\MollieFactory;

/**
 * @group MollieTest
 * @group Yves
 * @group Mollie
 * @group Mapper
 * @group ExpressCheckout
 * @group ExpressCheckoutOrderRequestMapperTest
 */
class ExpressCheckoutOrderRequestMapperTest extends Unit
{
    /**
     * @var string
     */
    protected const SHIPMENT_METHOD_KEY = 'spryker_dummy_shipment-express';

    /**
     * @return void
     */
    public function testMapsAddressesShipmentMethodKeyAndPaidAmount(): void
    {
        $mollieExpressCheckoutSessionTransfer = $this->createMollieExpressCheckoutSessionTransfer()
            ->setBillingAddress($this->createMollieAddressTransfer('Billing Street 1'))
            ->setShippingAddress($this->createMollieAddressTransfer('Shipping Street 2'));

        $mollieExpressCheckoutOrderRequestTransfer = $this->createExpressCheckoutOrderRequestMapper()
            ->mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
                $mollieExpressCheckoutSessionTransfer,
                new MollieExpressCheckoutOrderRequestTransfer(),
            );

        $billingAddressTransfer = $mollieExpressCheckoutOrderRequestTransfer->getBillingAddress();
        $this->assertSame('Jane', $billingAddressTransfer->getFirstName());
        $this->assertSame('Doe', $billingAddressTransfer->getLastName());
        $this->assertSame('Billing Street 1', $billingAddressTransfer->getAddress1());
        $this->assertSame('42000', $billingAddressTransfer->getZipCode());
        $this->assertSame('Varazdin', $billingAddressTransfer->getCity());
        $this->assertSame('HR', $billingAddressTransfer->getIso2Code());
        $this->assertSame('jane.doe@example.com', $billingAddressTransfer->getEmail());
        $this->assertSame('Shipping Street 2', $mollieExpressCheckoutOrderRequestTransfer->getShippingAddress()->getAddress1());
        $this->assertSame(static::SHIPMENT_METHOD_KEY, $mollieExpressCheckoutOrderRequestTransfer->getShipmentMethodKey());
        $this->assertSame(5498, $mollieExpressCheckoutOrderRequestTransfer->getExpectedGrandTotal());
    }

    /**
     * @return void
     */
    public function testMapsBillingAddressAsShippingAddressWhenSessionHasNoShippingAddress(): void
    {
        $mollieExpressCheckoutSessionTransfer = $this->createMollieExpressCheckoutSessionTransfer()
            ->setBillingAddress($this->createMollieAddressTransfer('Billing Street 1'));

        $mollieExpressCheckoutOrderRequestTransfer = $this->createExpressCheckoutOrderRequestMapper()
            ->mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
                $mollieExpressCheckoutSessionTransfer,
                new MollieExpressCheckoutOrderRequestTransfer(),
            );

        $this->assertSame('Billing Street 1', $mollieExpressCheckoutOrderRequestTransfer->getShippingAddress()->getAddress1());
    }

    /**
     * @return void
     */
    public function testMapsNoAddressesWhenSessionHasNone(): void
    {
        $mollieExpressCheckoutOrderRequestTransfer = $this->createExpressCheckoutOrderRequestMapper()
            ->mapMollieExpressCheckoutSessionTransferToMollieExpressCheckoutOrderRequestTransfer(
                $this->createMollieExpressCheckoutSessionTransfer(),
                new MollieExpressCheckoutOrderRequestTransfer(),
            );

        $this->assertNull($mollieExpressCheckoutOrderRequestTransfer->getBillingAddress());
        $this->assertNull($mollieExpressCheckoutOrderRequestTransfer->getShippingAddress());
    }

    /**
     * @return \Mollie\Yves\Mollie\Mapper\ExpressCheckout\ExpressCheckoutOrderRequestMapperInterface
     */
    protected function createExpressCheckoutOrderRequestMapper(): ExpressCheckoutOrderRequestMapperInterface
    {
        $factoryMock = $this->getMockBuilder(MollieFactory::class)
            ->onlyMethods(['getMollieService'])
            ->getMock();
        $factoryMock->method('getMollieService')->willReturn(new MollieService());

        return $factoryMock->createExpressCheckoutOrderRequestMapper();
    }

    /**
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer
     */
    protected function createMollieExpressCheckoutSessionTransfer(): MollieExpressCheckoutSessionTransfer
    {
        $mollieAmountTransfer = (new MollieAmountTransfer())
            ->setCurrency('EUR')
            ->setValue('54.98');
        $productMollieLinesTransfer = (new MollieLinesTransfer())
            ->setType('physical')
            ->setReference('not-a-shipment-method');
        $shippingFeeMollieLinesTransfer = (new MollieLinesTransfer())
            ->setType('shipping_fee')
            ->setReference(static::SHIPMENT_METHOD_KEY);

        return (new MollieExpressCheckoutSessionTransfer())
            ->setId('sess_7behWYUFsjn5hwRB86mXJ')
            ->setAmount($mollieAmountTransfer)
            ->addLine($productMollieLinesTransfer)
            ->addLine($shippingFeeMollieLinesTransfer);
    }

    /**
     * @param string $streetAndNumber
     *
     * @return \Generated\Shared\Transfer\MollieAddressTransfer
     */
    protected function createMollieAddressTransfer(string $streetAndNumber): MollieAddressTransfer
    {
        return (new MollieAddressTransfer())
            ->setGivenName('Jane')
            ->setFamilyName('Doe')
            ->setStreetAndNumber($streetAndNumber)
            ->setPostalCode('42000')
            ->setCity('Varazdin')
            ->setCountry('HR')
            ->setEmail('jane.doe@example.com');
    }
}
