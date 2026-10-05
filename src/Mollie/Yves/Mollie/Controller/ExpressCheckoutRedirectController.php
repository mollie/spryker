<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ExpressCheckoutRedirectController extends AbstractMollieController
{
    /**
     * @uses \SprykerShop\Yves\CartPage\Plugin\Router\CartPageRouteProviderPlugin::ROUTE_NAME_CART
     *
     * @var string
     */
    protected const ROUTE_NAME_CART = 'cart';

    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SUCCESS
     *
     * @var string
     */
    protected const ROUTE_NAME_CHECKOUT_SUCCESS = 'checkout-success';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ORDER_NOT_FOUND = 'Your express checkout order could not be found. Please contact us if you have been charged.';

    /**
     * Mollie redirects the shopper here after the express payment.
     * TODO (Step 2): create the order here from the completed Mollie session (full address + chosen shipping method).
     * Until then a quote with an order reference only exists if one was placed earlier; the checkout success page
     * shows it and clears the cart.
     * The payment itself is linked to the order by the express checkout webhook (MollieExpressCheckoutPaymentWebhookHandlerPlugin).
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function expressCheckoutRedirectAction(Request $request): RedirectResponse
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        if (!$quoteTransfer->getOrderReference()) {
            $this->addErrorMessage(static::ERROR_MESSAGE_ORDER_NOT_FOUND);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        return $this->redirectResponseInternal(static::ROUTE_NAME_CHECKOUT_SUCCESS);
    }
}
