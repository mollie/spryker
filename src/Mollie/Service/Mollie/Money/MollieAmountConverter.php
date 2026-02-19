<?php

declare(strict_types = 1);

namespace Mollie\Service\Mollie\Money;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface;

class MollieAmountConverter implements MollieAmountConverterInterface
{
    /**
     * @param \Spryker\Shared\Money\Converter\IntegerToDecimalConverterInterface $integerToDecimalConverter
     */
    public function __construct(protected IntegerToDecimalConverterInterface $integerToDecimalConverter)
    {
    }

    /**
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    public function convertIntegerToMollieAmount(int $amount): MollieAmountTransfer
    {
        $decimalAmount = $this->integerToDecimalConverter->convert($amount);
        $amount = number_format($decimalAmount, 2, '.', '');
        $mollieAmountTransfer = new MollieAmountTransfer();
        $mollieAmountTransfer->setValue($amount);

        return $mollieAmountTransfer;
    }
}
