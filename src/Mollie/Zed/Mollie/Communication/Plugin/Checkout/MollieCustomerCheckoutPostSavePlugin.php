<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedConfig;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieCustomerCheckoutPostSavePlugin extends AbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * {@inheritDoc}
     * - Runs only for a credit card payment with a new card where the shopper gave consent to save it.
     * - Skips when the quote's customer already has a Mollie customer.
     * - Creates or reuses the Mollie customer and sets it on `QuoteTransfer.customer.mollieCustomer`.
     * - Must be registered before `MollieCheckoutPostSavePlugin`, which sends the customer ID with the payment.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        $paymentTransfer = $quoteTransfer->getPayment();
        if ($paymentTransfer->getPaymentMethod() !== SharedConfig::MOLLIE_PAYMENT_CREDIT_CARD) {
            return;
        }

        $mollieCreditCardPaymentTransfer = $paymentTransfer->getMollieCreditCardPayment();
        if (!$mollieCreditCardPaymentTransfer->getIsSaveCardConsentGiven()) {
            return;
        }

        if ($mollieCreditCardPaymentTransfer->getMandateId()) {
            return;
        }

        if ($quoteTransfer->getCustomer()->getMollieCustomer()) {
            return;
        }

        $this->getFacade()->createMollieCustomer($quoteTransfer);
    }
}
