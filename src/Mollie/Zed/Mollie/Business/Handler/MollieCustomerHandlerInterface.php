<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieCustomerApiResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface MollieCustomerHandlerInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return MollieCustomerApiResponseTransfer
     */
    public function createCustomer(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): MollieCustomerApiResponseTransfer;
}