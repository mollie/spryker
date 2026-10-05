<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class ExpressCheckoutRedirectController extends AbstractMollieController
{
    use LoggerTrait;

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
    protected const MOLLIE_SESSION_STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SESSION_NOT_FOUND = 'Your express checkout could not be found. Please contact us if you have been charged.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PAYMENT_NOT_COMPLETED = 'Your express payment was not completed. You have not been charged.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ORDER_FAILED = 'Your order could not be created. Your payment will be refunded.';

    /**
     * Mollie redirects the shopper here after the express payment, i.e. after the shopper has been charged.
     * Creates the Spryker order from the completed Mollie session (full billing/shipping address and the shipping
     * method chosen in the sheet) and the shopper's cart, then shows the checkout success page (which clears the cart).
     * The payment is linked to the order by the express checkout webhook (MollieExpressCheckoutPaymentWebhookHandlerPlugin).
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function expressCheckoutRedirectAction(Request $request): RedirectResponse
    {
        $config = $this->getFactory()->getConfig();
        $quoteClient = $this->getFactory()->getQuoteClient();
        $session = $request->getSession();

        $mollieSessionId = $session->get($config->getExpressCheckoutSessionIdSessionKey());
        $expressCheckoutUuid = $session->get($config->getExpressCheckoutUuidSessionKey());

        if (!$mollieSessionId || !$expressCheckoutUuid) {
            if ($quoteClient->getQuote()->getOrderReference()) {
                return $this->redirectResponseInternal(static::ROUTE_NAME_CHECKOUT_SUCCESS);
            }

            $this->addErrorMessage(static::ERROR_MESSAGE_SESSION_NOT_FOUND);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        $mollieExpressCheckoutSessionTransfer = $this->findCompletedSession($mollieSessionId);

        if (!$mollieExpressCheckoutSessionTransfer) {
            $this->addErrorMessage(static::ERROR_MESSAGE_PAYMENT_NOT_COMPLETED);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        $mollieExpressCheckoutOrderRequestTransfer = $this->getFactory()
            ->createExpressCheckoutOrderRequestMapper()
            ->mapSessionToOrderRequest($mollieExpressCheckoutSessionTransfer, $quoteClient->getQuote(), $expressCheckoutUuid);

        $mollieExpressCheckoutOrderResponseTransfer = $this->getClient()->placeExpressCheckoutOrder($mollieExpressCheckoutOrderRequestTransfer);

        if (!$mollieExpressCheckoutOrderResponseTransfer->getIsSuccessful()) {
            // TODO (Step 3): refund the payment (tr_ id from the express webhook).
            $this->getLogger()->critical('Express checkout order could not be created after payment.', [
                'mollieSessionId' => $mollieSessionId,
                'expressCheckoutUuid' => $expressCheckoutUuid,
                'errors' => $mollieExpressCheckoutOrderResponseTransfer->getErrors(),
            ]);
            $this->addErrorMessage(static::ERROR_MESSAGE_ORDER_FAILED);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        // The checkout success step needs a confirmed, placed quote with the order reference.
        $quoteClient->setQuote(
            $mollieExpressCheckoutOrderResponseTransfer->getQuoteOrFail()
                ->setOrderReference($mollieExpressCheckoutOrderResponseTransfer->getOrderReferenceOrFail())
                ->setCheckoutConfirmed(true)
                ->setIsOrderPlacedSuccessfully(true),
        );
        $this->clearExpressCheckoutSession($request);

        return $this->redirectResponseInternal(static::ROUTE_NAME_CHECKOUT_SUCCESS);
    }

    /**
     * @param string $mollieSessionId
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutSessionTransfer|null
     */
    protected function findCompletedSession(string $mollieSessionId): ?MollieExpressCheckoutSessionTransfer
    {
        $mollieExpressCheckoutSessionApiResponseTransfer = $this->getClient()->getExpressCheckoutSession(
            (new MollieApiRequestTransfer())->setSessionId($mollieSessionId),
        );
        $mollieExpressCheckoutSessionTransfer = $mollieExpressCheckoutSessionApiResponseTransfer->getExpressCheckoutSession();

        if (
            !$mollieExpressCheckoutSessionApiResponseTransfer->getIsSuccessful()
            || $mollieExpressCheckoutSessionTransfer?->getStatus() !== static::MOLLIE_SESSION_STATUS_COMPLETED
        ) {
            return null;
        }

        return $mollieExpressCheckoutSessionTransfer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    protected function clearExpressCheckoutSession(Request $request): void
    {
        $config = $this->getFactory()->getConfig();

        $request->getSession()->remove($config->getExpressCheckoutSessionIdSessionKey());
        $request->getSession()->remove($config->getExpressCheckoutUuidSessionKey());
    }
}
