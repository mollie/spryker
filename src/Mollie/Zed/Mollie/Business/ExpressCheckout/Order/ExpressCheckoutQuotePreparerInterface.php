<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ExpressCheckoutQuotePreparerInterface
{
    /**
     * Specification:
     * - Runs the configured quote expanders on the request quote, in order.
     * - Recalculates the quote once afterwards.
     *
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @throws \Mollie\Zed\Mollie\Business\Exception\ExpressCheckoutOrderException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function prepareQuote(MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer): QuoteTransfer;
}
