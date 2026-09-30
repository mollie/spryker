<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieExpressCheckoutOrderRequestTransfer;
use Mollie\Yves\Mollie\Form\ExpressCheckout\ExpressCheckoutAddressForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class ExpressCheckoutAddressController extends AbstractMollieController
{
    /**
     * @uses \SprykerShop\Yves\CartPage\Plugin\Router\CartPageRouteProviderPlugin::ROUTE_NAME_CART
     *
     * @var string
     */
    protected const ROUTE_NAME_CART = 'cart';

    /**
     * @var string
     */
    protected const SUCCESS_MESSAGE_ADDRESSES_SAVED = 'Addresses saved. You can now use express checkout.';

    /**
     * Saves the billing and shipping address from the cart's express checkout address form on the quote
     * (with the first available shipment method, so the cart total includes shipping) and returns to the cart.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveAddressesAction(Request $request): RedirectResponse
    {
        $quoteClient = $this->getFactory()->getQuoteClient();
        $quoteTransfer = $quoteClient->getQuote();

        $addressForm = $this->getFactory()
            ->getExpressCheckoutAddressForm($quoteTransfer)
            ->handleRequest($request);

        if (!$addressForm->isSubmitted() || !$addressForm->isValid()) {
            $this->addFormErrorMessages($addressForm);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        $formData = $addressForm->getData();
        $billingAddressTransfer = $formData[ExpressCheckoutAddressForm::FIELD_BILLING_ADDRESS];

        // zasto ovaj format (clone) ????
        $shippingAddressTransfer = $formData[ExpressCheckoutAddressForm::FIELD_SHIPPING_SAME_AS_BILLING]
            ? clone $billingAddressTransfer
            : $formData[ExpressCheckoutAddressForm::FIELD_SHIPPING_ADDRESS];

        $mollieExpressCheckoutQuoteResponseTransfer = $this->getClient()->saveExpressCheckoutAddresses(
            (new MollieExpressCheckoutOrderRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setBillingAddress($billingAddressTransfer)
                ->setShippingAddress($shippingAddressTransfer),
        );

        if (!$mollieExpressCheckoutQuoteResponseTransfer->getIsSuccessful()) {
            foreach ($mollieExpressCheckoutQuoteResponseTransfer->getErrors() as $error) {
                $this->addErrorMessage($error);
            }

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        $quoteClient->setQuote($mollieExpressCheckoutQuoteResponseTransfer->getQuoteOrFail());
        $this->addSuccessMessage(static::SUCCESS_MESSAGE_ADDRESSES_SAVED);

        return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return void
     */
    protected function addFormErrorMessages(FormInterface $form): void
    {
        foreach ($form->getErrors(true) as $formError) {
            $field = $formError->getOrigin()?->getName();
            $this->addErrorMessage($field ? sprintf('%s: %s', $field, $formError->getMessage()) : $formError->getMessage());
        }
    }
}
