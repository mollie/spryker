<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\ExpressCheckout;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ExpressCheckoutAddressChecker implements ExpressCheckoutAddressCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function hasAddresses(QuoteTransfer $quoteTransfer): bool
    {
        return $this->isFilled($quoteTransfer->getBillingAddress())
            && $this->isFilled($quoteTransfer->getShippingAddress());
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return bool
     */
    protected function isFilled(?AddressTransfer $addressTransfer): bool
    {
        return $addressTransfer !== null
            && $addressTransfer->getAddress1()
            && $addressTransfer->getCity()
            && $addressTransfer->getIso2Code();
    }
}
