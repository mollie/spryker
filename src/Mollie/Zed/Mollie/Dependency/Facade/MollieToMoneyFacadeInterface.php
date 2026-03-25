<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

interface MollieToMoneyFacadeInterface
{
    /**
     * Specification:
     * - Converts a decimal value into integer value
     *
     * @api
     *
     * @param float $value
     *
     * @return int
     */
    public function convertDecimalToInteger($value): int;

    /**
     * Specification:
     * - Converts an integer value into decimal value
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal($value): float;
}
