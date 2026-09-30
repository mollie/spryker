<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\QuoteTransfer;

interface MollieToCalculationFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param bool $executeQuotePlugins
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function recalculateQuote(QuoteTransfer $quoteTransfer, bool $executeQuotePlugins = true): QuoteTransfer;
}
