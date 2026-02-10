<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler\Payment;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentEpsHandler implements MolliePaymentHandlerInterface
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
            ->setPaymentProvider(MollieConfig::MOLLIE_PROVIDER_EPS)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_EPS);

        return $quoteTransfer;
    }
}
