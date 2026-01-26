<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Filter;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface MolliePaymentMethodsFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function applyFilter(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer;
}
