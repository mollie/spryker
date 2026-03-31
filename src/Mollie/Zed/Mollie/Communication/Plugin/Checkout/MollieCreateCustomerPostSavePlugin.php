<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Business\MollieFacadeInterface;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method MollieFacadeInterface getFacade()
 */
class MollieCreateCustomerPostSavePlugin extends AbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        if(!$quoteTransfer->getMollieSingleClickPaymentConsent()){
            return;
        }
        
        $this->getFacade()->createCustomer($quoteTransfer, $checkoutResponseTransfer);
    }
}
