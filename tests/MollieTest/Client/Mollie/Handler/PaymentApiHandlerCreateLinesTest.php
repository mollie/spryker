<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie\Handler;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
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
