<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Handler;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Client\Mollie\Handler\PaymentApiHandler;
use Mollie\Client\Mollie\MollieConfig;
use Mollie\Service\Mollie\MollieServiceInterface;

/**
 * @group MollieTest
 * @group Client
 * @group Mollie
 * @group Handler
 * @group PaymentApiHandlerCreateLinesTest
 */
class PaymentApiHandlerCreateLinesTest extends Unit
{
    protected const string CURRENCY_EUR = 'EUR';

    /**
     * A line with quantity > 1 was sent to Mollie with its totalAmount aggregated over all
     * units (sumPriceToPayAggregation) but its vatAmount taken from a single unit
     * (unitTaxAmount), so Mollie rejected the payment with HTTP 422:
     *
     *   "Line item 1 is invalid. The 'vatAmount' field is off.
     *    Expected to be 111.75 (699.90 x (19.00 / 119.00)), got 55.87"
     *
     * The line's vatAmount must be the full aggregated tax (sumTaxAmountFullAggregation)
     * so it matches the aggregated totalAmount. This bug affected BNPL payments since lines
     * were introduced; PR #102 broadened line-sending to all payment methods, surfacing it
     * more widely.
     *
     * @return void
     */
    public function testMultiQuantityLineUsesAggregatedVatAmount(): void
    {
        $paymentApiHandler = $this->createHandler();
        $quoteTransfer = (new QuoteTransfer())
            ->setCurrency((new CurrencyTransfer())->setCode(self::CURRENCY_EUR))
            ->setItems(new ArrayObject([
                (new ItemTransfer())
                    ->setSku('202500000066')
                    ->setName('Twin Stroller')
                    ->setUnitPrice(34995)
                    ->setSumPriceToPayAggregation(69990)
                    ->setUnitDiscountAmountAggregation(0)
                    ->setUnitTaxAmount(5587)
                    ->setSumTaxAmountFullAggregation(11175)
                    ->setQuantity(2)
                    ->setTaxRate(19.0),
            ]));

        $lines = $paymentApiHandler->createLines($quoteTransfer, 'paypal')->toArray();

        $this->assertCount(1, $lines);
        $this->assertSame('699.90', $lines[0]['totalAmount']['value']);
        $this->assertSame('111.75', $lines[0]['vatAmount']['value']);
    }

    /**
     * Cart-level reductions (gift cards, vouchers) can lower the grand total
     * below the sum of per-item totals. Mollie rejects the payment with HTTP
     * 422 when the amount does not equal the sum of line totals, so a negative
     * discount line must balance the gap.
     *
     * @return void
     */
    public function testAddsDiscountLineWhenGrandTotalIsBelowLineSum(): void
    {
        $paymentApiHandler = $this->createHandler();

        $grandTotal = 12080;

        $quoteTransfer = (new QuoteTransfer())
            ->setCurrency((new CurrencyTransfer())->setCode(self::CURRENCY_EUR))
            ->setItems(new ArrayObject([
                (new ItemTransfer())
                    ->setSku('SKU001')
                    ->setName('Item one')
                    ->setUnitPrice(11990)
                    ->setSumPriceToPayAggregation(10791)
                    ->setUnitDiscountAmountAggregation(1199)
                    ->setSumTaxAmountFullAggregation(1723)
                    ->setQuantity(1)
                    ->setTaxRate(19.0),
                (new ItemTransfer())
                    ->setSku('SKU002')
                    ->setName('Item two')
                    ->setUnitPrice(1499)
                    ->setSumPriceToPayAggregation(1349)
                    ->setUnitDiscountAmountAggregation(150)
                    ->setSumTaxAmountFullAggregation(215)
                    ->setQuantity(1)
                    ->setTaxRate(19.0),
            ]))
            ->setPayment((new PaymentTransfer())->setAmount($grandTotal));

        $lines = $paymentApiHandler->createLines($quoteTransfer, 'creditcard')->toArray();

        $this->assertCount(3, $lines);
        $this->assertSame('discount', $lines[2]['type']);
        $this->assertSame('-0.60', $lines[2]['totalAmount']['value']);

        $sum = 0.0;
        foreach ($lines as $line) {
            $sum += (float)$line['totalAmount']['value'];
        }
        $this->assertSame(number_format($grandTotal / 100, 2), number_format($sum, 2));
    }

    /**
     * No discount line is added when the line sum already equals the grand
     * total — the common case where no cart-level reduction is applied.
     *
     * @return void
     */
    public function testDoesNotAddDiscountLineWhenAmountsMatch(): void
    {
        $paymentApiHandler = $this->createHandler();

        $itemTotal = 10791;

        $quoteTransfer = (new QuoteTransfer())
            ->setCurrency((new CurrencyTransfer())->setCode(self::CURRENCY_EUR))
            ->setItems(new ArrayObject([
                (new ItemTransfer())
                    ->setSku('SKU001')
                    ->setName('Item one')
                    ->setUnitPrice(11990)
                    ->setSumPriceToPayAggregation($itemTotal)
                    ->setUnitDiscountAmountAggregation(1199)
                    ->setSumTaxAmountFullAggregation(1723)
                    ->setQuantity(1)
                    ->setTaxRate(19.0),
            ]))
            ->setPayment((new PaymentTransfer())->setAmount($itemTotal));

        $lines = $paymentApiHandler->createLines($quoteTransfer, 'creditcard')->toArray();

        $this->assertCount(1, $lines);
        $this->assertSame('physical', $lines[0]['type']);
    }

    /**
     * @return \Mollie\Client\Mollie\Handler\PaymentApiHandler
     */
    protected function createHandler(): PaymentApiHandler
    {
        $mollieService = $this->createMock(MollieServiceInterface::class);
        $mollieService->method('convertIntegerToMollieAmount')
            ->willReturnCallback(function (int $value, ?string $currency = null): MollieAmountTransfer {
                return (new MollieAmountTransfer())
                    ->setValue(number_format($value / 100, 2))
                    ->setCurrency($currency ?? self::CURRENCY_EUR);
            });

        $mollieConfig = $this->createMock(MollieConfig::class);

        return new PaymentApiHandler($mollieService, $mollieConfig);
    }
}
