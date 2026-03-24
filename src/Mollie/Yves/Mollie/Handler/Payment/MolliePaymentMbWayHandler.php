<?php

namespace Mollie\Yves\Mollie\Handler\Payment;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentMbWayHandler implements MolliePaymentHandlerInterface
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
            ->setPaymentProvider(MollieConfig::MOLLIE_PAYMENT_MB_WAY_PROVIDER)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_MB_WAY);

        return $quoteTransfer;
    }
}
