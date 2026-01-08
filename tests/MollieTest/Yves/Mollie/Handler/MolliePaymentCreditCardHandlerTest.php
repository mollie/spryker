<?php

namespace MollieTest\Yves\Mollie\Handler;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Yves\Mollie\Handler\MolliePaymentCreditCardHandler;

class MolliePaymentCreditCardHandlerTest extends Unit
{
    /**
     * @return void
     */
    public function testAddPaymentToQuoteSetsCorrectPaymentProviderAndMethod(): void
    {
        $paymentHandler = new MolliePaymentCreditCardHandler();

        $quoteTransfer = (new QuoteTransfer())
            ->setPayment(new PaymentTransfer());

        $result = $paymentHandler->addPaymentToQuote($quoteTransfer);
        $paymentTransfer = $result->getPayment();

        $this->assertSame(MollieConfig::PROVIDER_NAME, $paymentTransfer->getPaymentProvider());
        $this->assertSame(MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD, $paymentTransfer->getPaymentMethod());
    }
}
