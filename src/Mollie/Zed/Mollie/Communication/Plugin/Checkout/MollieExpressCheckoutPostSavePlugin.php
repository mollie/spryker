<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieExpressCheckoutPostSavePlugin extends AbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        $paymentTransfer = $quoteTransfer->getPaymentOrFail();
        $paymentMethod = $paymentTransfer->getPaymentMethodOrFail();

        if (!$this->getConfig()->isMollieExpressPaymentMethod($paymentMethod)) {
            return;
        }

        $this->getFacade()->createExpressCheckoutMolliePayment($quoteTransfer, $checkoutResponseTransfer);
    }
}
