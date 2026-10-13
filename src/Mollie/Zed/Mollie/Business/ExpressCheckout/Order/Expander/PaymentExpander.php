<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Order\Expander;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Generated\Shared\Transfer\MollieExpressPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;

class PaymentExpander implements ExpressCheckoutQuoteExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        MollieExpressCheckoutOrderRequestTransfer $mollieExpressCheckoutOrderRequestTransfer,
    ): QuoteTransfer {
        $paymentTransfer = (new PaymentTransfer())
            ->setPaymentProvider(SharedMollieConfig::MOLLIE_PROVIDER_EXPRESS)
            ->setPaymentMethod(SharedMollieConfig::MOLLIE_PAYMENT_EXPRESS)
            ->setPaymentSelection(SharedMollieConfig::MOLLIE_PAYMENT_EXPRESS)
            ->setMollieExpressPayment(
                (new MollieExpressPaymentTransfer())
                    ->setExpressCheckoutUuid($mollieExpressCheckoutOrderRequestTransfer->getExpressCheckoutUuidOrFail()),
            );

        return $quoteTransfer->setPayment($paymentTransfer);
    }
}
