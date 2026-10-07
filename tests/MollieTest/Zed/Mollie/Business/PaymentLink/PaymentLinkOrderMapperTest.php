<?php

declare(strict_types = 1);

namespace MollieTest\Zed\Mollie\Business\PaymentLink;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\OrderBuilder;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
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
     * @var \MollieTest\Zed\Mollie\MollieZedTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testMapLinesSumUpToGrandTotal(): void
    {
        $orderTransfer = $this->createOrderTransfer();

        $mollieLinesTransfers = $this->createPaymentLinkOrderMapper()->mapOrderItemsAndExpensesToMollieLines($orderTransfer);

        $moneyFacade = $this->tester->getLocator()->money()->facade();

        $sumOfIntegerLineTotals = 0;
        foreach ($mollieLinesTransfers as $mollieLinesTransfer) {
            $integerLineTotal = $moneyFacade->convertDecimalToInteger((float)$mollieLinesTransfer->getTotalAmount()->getValue());
            $sumOfIntegerLineTotals += $integerLineTotal;
        }

        $this->assertCount(2, $mollieLinesTransfers);
        $this->assertSame($orderTransfer->getTotals()->getGrandTotal(), $sumOfIntegerLineTotals);
    }

    /**
     * @return void
     */
    public function testMapItemLineFromPriceToPayAmounts(): void
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
        $this->assertSame(static::CURRENCY_CODE, $itemMollieLinesTransfer->getTotalAmount()->getCurrency());
        $this->assertNull($itemMollieLinesTransfer->getDiscountAmount());
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
        $orderTransfer = (new OrderBuilder())
            ->withItem([
                ItemTransfer::NAME => 'Office chair',
                ItemTransfer::SKU => 'chair-001',
                ItemTransfer::QUANTITY => 2,
                ItemTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 10710,
                ItemTransfer::SUM_PRICE_TO_PAY_AGGREGATION => 21420,
            ])
            ->withExpense([
                ExpenseTransfer::TYPE => ShipmentConfig::SHIPMENT_EXPENSE_TYPE,
                ExpenseTransfer::NAME => 'Standard',
                ExpenseTransfer::QUANTITY => 1,
                ExpenseTransfer::UNIT_PRICE_TO_PAY_AGGREGATION => 595,
                ExpenseTransfer::SUM_PRICE_TO_PAY_AGGREGATION => 595,
            ])
            ->withTotals([
                TotalsTransfer::GRAND_TOTAL => 22015,
            ])
            ->withCurrency([
                CurrencyTransfer::CODE => static::CURRENCY_CODE,
            ])
            ->build();

        return $orderTransfer;
    }
}
