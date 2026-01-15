<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;

class MolliePaymentKlarnaPayNowHandler implements MolliePaymentHandlerInterface
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
            ->setPaymentProvider(MollieConfig::PROVIDER_NAME)
            ->setPaymentMethod(MollieConfig::MOLLIE_PAYMENT_KLARNA_PAY_NOW);

        return $quoteTransfer;
    }
}
