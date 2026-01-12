<?php

declare(strict_types=1);

namespace Mollie\Service\Mollie;

interface MollieServiceInterface
{
    /**
     * Calls IntegerToDecimalConverter class from shared layer
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal(int $value): float;
}
