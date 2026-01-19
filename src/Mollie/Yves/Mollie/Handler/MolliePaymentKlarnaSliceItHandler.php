<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentKlarnaSliceItHandler implements MolliePaymentHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentTransfer = $quoteTransfer->getPayment();

        $paymentTransfer
            ->setPaymentProvider(MollieConfig::MOLLIE_PROVIDER_KLARNA_SLICE_IT)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_KLARNA_SLICE_IT);

        return $quoteTransfer;
    }
}
