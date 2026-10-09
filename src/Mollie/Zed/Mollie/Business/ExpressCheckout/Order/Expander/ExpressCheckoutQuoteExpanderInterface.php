<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ExpressCheckoutQuoteExpanderInterface
{
    /**
     * Specification:
     * - Prepares one aspect of the quote so it can be placed as an express checkout order.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @throws \Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): QuoteTransfer;
}
