<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class ExpressCheckoutRedirectController extends AbstractMollieController
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
    protected const KEY_ID = 'id';

    /**
     * Mollie redirects the shopper here after the express payment.
     *
     * TODO: discovery only - fetches the session and its payment so their raw payloads (addresses, payment id,
     * method) end up in the Mollie API log, then returns to the cart. Order creation and refund come next.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function expressCheckoutRedirectAction(Request $request): RedirectResponse
    {
        $sessionId = $request->getSession()->get($this->getFactory()->getConfig()->getExpressCheckoutSessionIdSessionKey());

        if (!$sessionId) {
            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

        $mollieExpressCheckoutSessionApiResponseTransfer = $this->getClient()->getExpressCheckoutSession(
            (new MollieApiRequestTransfer())->setSessionId($sessionId),
        );

        $paymentId = $this->findPaymentId($mollieExpressCheckoutSessionApiResponseTransfer);

        if ($paymentId) {
            $this->getClient()->getPaymentByTransactionId(
                (new MollieApiRequestTransfer())->setTransactionId($paymentId),
            );
        }

        return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutSessionApiResponseTransfer $mollieExpressCheckoutSessionApiResponseTransfer
     *
     * @return string|null
     */
    protected function findPaymentId(
        MollieExpressCheckoutSessionApiResponseTransfer $mollieExpressCheckoutSessionApiResponseTransfer,
    ): ?string {
        $mollieExpressCheckoutSessionTransfer = $mollieExpressCheckoutSessionApiResponseTransfer->getExpressCheckoutSession();

        if (!$mollieExpressCheckoutSessionApiResponseTransfer->getIsSuccessful() || !$mollieExpressCheckoutSessionTransfer) {
            return null;
        }

        $paymentId = $mollieExpressCheckoutSessionTransfer->getPayment()[static::KEY_ID] ?? null;

        if ($paymentId) {
            return $paymentId;
        }

        $paymentHref = $mollieExpressCheckoutSessionTransfer->getLinks()?->getPayment()?->getHref();

        return $paymentHref ? basename((string)parse_url($paymentHref, PHP_URL_PATH)) : null;
    }
}
