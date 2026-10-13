<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 */
class MollieExpressCheckoutDoSaveOrderPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $paymentTransfer = $quoteTransfer->getPaymentOrFail();
        $paymentMethod = $paymentTransfer->getPaymentMethodOrFail();

        if (!$this->getConfig()->isMollieExpressPaymentMethod($paymentMethod)) {
            return;
        }

        $this->getFacade()->createExpressCheckoutMolliePayment($quoteTransfer, $saveOrderTransfer);
    }
}
