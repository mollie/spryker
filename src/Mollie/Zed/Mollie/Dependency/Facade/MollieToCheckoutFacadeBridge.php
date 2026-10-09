<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;

class MollieToCheckoutFacadeBridge implements MollieToCheckoutFacadeInterface
{
    protected CheckoutFacadeInterface $checkoutFacade;

    /**
     * @param \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface $checkoutFacade
     */
    public function __construct(CheckoutFacadeInterface $checkoutFacade)
    {
        $this->checkoutFacade = $checkoutFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function placeOrder(QuoteTransfer $quoteTransfer): CheckoutResponseTransfer
    {
        return $this->checkoutFacade->placeOrder($quoteTransfer);
    }
}
