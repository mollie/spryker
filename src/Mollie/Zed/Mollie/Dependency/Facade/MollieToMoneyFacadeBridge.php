<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class MollieToMoneyFacadeBridge implements MollieToMoneyFacadeInterface
{
    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected MoneyFacadeInterface $moneyFacade;

    /**
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     */
    public function __construct($moneyFacade)
    {
        $this->moneyFacade = $moneyFacade;
    }

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
    public function convertDecimalToInteger($value): int
    {
        return $this->moneyFacade->convertDecimalToInteger($value);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal($value): float
    {
        return $this->moneyFacade->convertIntegerToDecimal($value);
    }
}
