<?php

declare(strict_types = 1);

namespace Mollie\Service\Mollie\Money;

use Generated\Shared\Transfer\MollieAmountTransfer;

interface MollieAmountConverterInterface
{
    /**
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    public function convertIntegerToMollieAmount(int $amount): MollieAmountTransfer;
}
