<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler\Payment;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentPayByBankHandler implements MolliePaymentHandlerInterface
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
            ->setPaymentProvider(MollieConfig::MOLLIE_PROVIDER_PAY_BY_BANK)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_PAY_BY_BANK);

        return $quoteTransfer;
    }
}
