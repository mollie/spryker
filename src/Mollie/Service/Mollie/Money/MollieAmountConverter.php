<?php

declare(strict_types = 1);

namespace Mollie\Service\Mollie\Money;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Spryker\Shared\Money\Converter\DecimalToIntegerConverterInterface;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface;

class MollieAmountConverter implements MollieAmountConverterInterface
{
    /**
     * @param \Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface $integerToDecimalConverter
     * @param \Spryker\Shared\Money\Converter\DecimalToIntegerConverterInterface $decimalToIntegerConverter
     */
    public function __construct(
        protected IntegerToDecimalConverterInterface $integerToDecimalConverter,
        protected DecimalToIntegerConverterInterface $decimalToIntegerConverter,
    ) {
    }

    /**
     * @param int $amount
     * @param string|null $currency
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    public function convertIntegerToMollieAmount(int $amount, ?string $currency = null): MollieAmountTransfer
    {
        $decimalAmount = $this->integerToDecimalConverter->convert($amount);
        $amount = number_format($decimalAmount, 2, '.', '');
        $mollieAmountTransfer = new MollieAmountTransfer();
        $mollieAmountTransfer->setValue($amount);

        if ($currency) {
            $mollieAmountTransfer->setCurrency($currency);
        }

        return $mollieAmountTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieAmountTransfer $mollieAmountTransfer
     *
     * @return int
     */
    public function convertMollieAmountToInteger(MollieAmountTransfer $mollieAmountTransfer): int
    {
        $decimalAmount = (float)$mollieAmountTransfer->getValue();

        return $this->decimalToIntegerConverter->convert($decimalAmount);
    }
}
