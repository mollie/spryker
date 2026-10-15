<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToCalculationFacadeInterface;

class ExpressCheckoutQuotePreparer implements ExpressCheckoutQuotePreparerInterface
{
    /**
     * @param array<\Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander\ExpressCheckoutQuoteExpanderInterface> $quoteExpanders
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToCalculationFacadeInterface $calculationFacade
     */
    public function __construct(
        protected array $quoteExpanders,
        protected MollieToCalculationFacadeInterface $calculationFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function prepareQuote(MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer): QuoteTransfer
    {
        $quoteTransfer = $mollieExpressCheckoutOrderRequestTransfer->getQuoteOrFail();

        foreach ($this->quoteExpanders as $quoteExpander) {
            $quoteTransfer = $quoteExpander->expand($quoteTransfer, $mollieExpressCheckoutOrderRequestTransfer);
        }

        return $this->calculationFacade->recalculateQuote($quoteTransfer->setSkipRecalculation(false));
    }
}
