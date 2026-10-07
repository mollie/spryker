<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\PaymentLink;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\Mapper\PaymentLink\PaymentLinkOrderMapperInterface;
use Mollie\Zed\Mollie\Business\MollieBusinessFactory;
use Spryker\Shared\Shipment\ShipmentConfig;

class PaymentLinkOrderMapperTest extends Unit
{
    /**
     * @var string
     */
    protected const CURRENCY_CODE = 'EUR';

    /**
     * @var string
     */
    protected const THRESHOLD_EXPENSE_TYPE = 'THRESHOLD_EXPENSE_TYPE';

    /**
     * @var string
     */
    protected const ORDER_EMAIL = 'buyer@example.com';

    /**
     * @return void
     */
    public function testMapOrderItemsAndExpensesToMollieLinesSumsUpToGrandTotal(): void
    {
        $orderTransfer = $this->createOrderTransfer();

        $mollieLinesTransfers = $this->createPaymentLinkOrderMapper()->mapOrderItemsAndExpensesToMollieLines($orderTransfer);

        $sumOfIntegerLineTotals = 0;
        foreach ($mollieLinesTransfers as $mollieLinesTransfer) {
            $sumOfIntegerLineTotals += $this->convertMollieAmountToInteger($mollieLinesTransfer->getTotalAmount());
        }

        $this->assertCount(4, $mollieLinesTransfers);
        $this->assertSame($orderTransfer->getTotals()->getGrandTotal(), $sumOfIntegerLineTotals);
    }

    /**
     * @return void
     */
    public function testMapOrderItemsAndExpensesToMollieLinesMapsItemFromPriceToPayFields(): void
    {
        $orderTransfer = $this->createOrderTransfer();

        $mollieLinesTransfers = $this->createPaymentLinkOrderMapper()->mapOrderItemsAndExpensesToMollieLines($orderTransfer);
        $itemMollieLinesTransfer = $mollieLinesTransfers->offsetGet(0);

        $this->assertSame(MollieConstants::PRODUCT_TYPE_PHYSICAL, $itemMollieLinesTransfer->getType());
        $this->assertSame('Office chair', $itemMollieLinesTransfer->getDescription());
        $this->assertSame('chair-001', $itemMollieLinesTransfer->getSku());
        $this->assertSame(2, $itemMollieLinesTransfer->getQuantity());
        $this->assertSame('107.10', $itemMollieLinesTransfer->getUnitPrice()->getValue());
        $this->assertSame('214.20', $itemMollieLinesTransfer->getTotalAmount()->getValue());
        $this->assertSame('19.00', $itemMollieLinesTransfer->getVatRate());
        $this->assertSame('34.20', $itemMollieLinesTransfer->getVatAmount()->getValue());
        $this->assertSame(static::CURRENCY_CODE, $itemMollieLinesTransfer->getTotalAmount()->getCurrency());
        $this->assertNull($itemMollieLinesTransfer->getDiscountAmount());
    }

    /**
     * @return void
     */
    public function testMapOrderItemsAndExpensesToMollieLinesFormatsDecimalStringTaxRateFromPersistedOrder(): void
    {
        $orderTransfer = $this->createOrderTransfer();
        $orderTransfer->getItems()->offsetGet(0)->setTaxRate('19.00');
        $orderTransfer->getExpenses()->offsetGet(0)->setTaxRate('19.00');

        $mollieLinesTransfers = $this->createPaymentLinkOrderMapper()->mapOrderItemsAndExpensesToMollieLines($orderTransfer);

        $this->assertSame('19.00', $mollieLinesTransfers->offsetGet(0)->getVatRate());
        $this->assertSame('19.00', $mollieLinesTransfers->offsetGet(2)->getVatRate());
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\Mapper\PaymentLink\PaymentLinkOrderMapperInterface
     */
    protected function createPaymentLinkOrderMapper(): PaymentLinkOrderMapperInterface
    {
        $paymentLinkOrderMapper = $this->createMollieBusinessFactory()->createPaymentLinkOrderMapper();

        return $paymentLinkOrderMapper;
    }

    /**
     * @return \Mollie\Zed\Mollie\Business\MollieBusinessFactory
     */
    protected function createMollieBusinessFactory(): MollieBusinessFactory
    {
        $mollieBusinessFactory = new MollieBusinessFactory();

        return $mollieBusinessFactory;
    }

    /**
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function createOrderTransfer(): OrderTransfer
    {
        $chairItemTransfer = new ItemTransfer();
        $chairItemTransfer
            ->setName('Office chair')
            ->setSku('chair-001')
            ->setQuantity(2)
            ->setUnitPriceToPayAggregation(10710)
            ->setSumPriceToPayAggregation(21420)
            ->setSumTaxAmountFullAggregation(3420)
            ->setTaxRate(19.0);

        $bookItemTransfer = new ItemTransfer();
        $bookItemTransfer
            ->setName('Product catalogue')
            ->setSku('book-001')
            ->setQuantity(1)
            ->setUnitPriceToPayAggregation(1070)
            ->setSumPriceToPayAggregation(1070)
            ->setSumTaxAmountFullAggregation(70)
            ->setTaxRate(7.0);

        $shipmentExpenseTransfer = new ExpenseTransfer();
        $shipmentExpenseTransfer
            ->setType(ShipmentConfig::SHIPMENT_EXPENSE_TYPE)
            ->setName('Standard')
            ->setQuantity(1)
            ->setUnitPriceToPayAggregation(595)
            ->setSumPriceToPayAggregation(595)
            ->setSumTaxAmount(95)
            ->setTaxRate(19.0);

        $thresholdExpenseTransfer = new ExpenseTransfer();
        $thresholdExpenseTransfer
            ->setType(static::THRESHOLD_EXPENSE_TYPE)
            ->setName('Small order fee')
            ->setQuantity(1)
            ->setUnitPriceToPayAggregation(1190)
            ->setSumPriceToPayAggregation(1190)
            ->setSumTaxAmount(190)
            ->setTaxRate(19.0);

        $billingAddressTransfer = new AddressTransfer();
        $billingAddressTransfer
            ->setSalutation('Mr')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setCompany('Acme GmbH')
            ->setAddress1('Julie-Wolfthorn-Straße')
            ->setAddress2('1')
            ->setAddress3('Building B')
            ->setZipCode('10115')
            ->setCity('Berlin')
            ->setIso2Code('DE');

        $totalsTransfer = new TotalsTransfer();
        $totalsTransfer
            ->setGrandTotal(24275);

        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer
            ->setCode(static::CURRENCY_CODE);

        $orderTransfer = new OrderTransfer();
        $orderTransfer
            ->addItem($chairItemTransfer)
            ->addItem($bookItemTransfer)
            ->addExpense($shipmentExpenseTransfer)
            ->addExpense($thresholdExpenseTransfer)
            ->setBillingAddress($billingAddressTransfer)
            ->setEmail(static::ORDER_EMAIL)
            ->setTotals($totalsTransfer)
            ->setCurrency($currencyTransfer);

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer $mollieAmountTransfer
     *
     * @return int
     */
    protected function convertMollieAmountToInteger(MollieAmountTransfer $mollieAmountTransfer): int
    {
        $decimalValue = (float)$mollieAmountTransfer->getValue();
        $integerValue = (int)round($decimalValue * 100);

        return $integerValue;
    }
}
