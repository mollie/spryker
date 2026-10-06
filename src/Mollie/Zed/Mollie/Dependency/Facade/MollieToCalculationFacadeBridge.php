<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;

class MollieToCalculationFacadeBridge implements MollieToCalculationFacadeInterface
{
    protected CalculationFacadeInterface $calculationFacade;

    /**
     * @param \Spryker\Zed\Calculation\Business\CalculationFacadeInterface $calculationFacade
     */
    public function __construct(CalculationFacadeInterface $calculationFacade)
    {
        $this->calculationFacade = $calculationFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function recalculateQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->calculationFacade->recalculateQuote($quoteTransfer);
    }
}
