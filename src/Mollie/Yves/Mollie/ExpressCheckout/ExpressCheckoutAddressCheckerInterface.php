<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\ExpressCheckout;

use Generated\Shared\Transfer\QuoteTransfer;

interface ExpressCheckoutAddressCheckerInterface
{
    /**
     * Specification:
     * - Returns true when the quote has the billing and shipping address saved by the express checkout address form.
     * - Express checkout is only offered (buttons rendered, Mollie session created) when this returns true.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function hasAddresses(QuoteTransfer $quoteTransfer): bool;
}
