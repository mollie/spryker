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
        if (!$mollieCreditCardPaymentTransfer->getIsSaveCardConsentGiven() || $mollieCreditCardPaymentTransfer->getMandateId()) {
            return;
        }

        $customerTransfer = $this->getFacade()->expandCustomerWithMollieCustomer($quoteTransfer->getCustomer());
        if ($customerTransfer->getMollieCustomer()) {
            return;
        }

        $mollieCustomerTransfer = $this->getFacade()->createMollieCustomer($quoteTransfer);
        $customerTransfer->setMollieCustomer($mollieCustomerTransfer);
    }
}
