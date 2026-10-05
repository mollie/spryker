<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
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
    protected const ERROR_MESSAGE_ORDER_FAILED_REFUNDED = 'Your order could not be created. Your payment has been refunded.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ORDER_FAILED_NOT_REFUNDED = 'Your order could not be created. Please contact us, your payment will be refunded.';

    /**
     * @var int
     */
    protected const REFUND_ATTEMPTS = 5;

    /**
     * @var int
     */
    protected const REFUND_RETRY_DELAY_SECONDS = 2;

    /**
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
            $this->getLogger()->critical('Express checkout order could not be created after payment.', [
                'mollieSessionId' => $mollieSessionId,
                'expressCheckoutUuid' => $expressCheckoutUuid,
                'errors' => $mollieExpressCheckoutOrderResponseTransfer->getErrors(),
            ]);

            $this->addErrorMessage($this->refundPayment($expressCheckoutUuid)
                ? static::ERROR_MESSAGE_ORDER_FAILED_REFUNDED
                : static::ERROR_MESSAGE_ORDER_FAILED_NOT_REFUNDED);
            $this->clearExpressCheckoutSession($request);

            return $this->redirectResponseInternal(static::ROUTE_NAME_CART);
        }

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

    /**
     * @param string $expressCheckoutUuid
     *
     * @return bool
     */
    protected function refundPayment(string $expressCheckoutUuid): bool
    {
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid($expressCheckoutUuid);

        for ($attempt = 1; $attempt <= static::REFUND_ATTEMPTS; $attempt++) {
            $mollieExpressCheckoutRefundResponseTransfer = $this->getClient()
                ->refundExpressCheckoutPayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);

            if ($mollieExpressCheckoutRefundResponseTransfer->getIsPaymentKnown()) {
                return (bool)$mollieExpressCheckoutRefundResponseTransfer->getIsSuccessful();
            }

            if ($attempt < static::REFUND_ATTEMPTS) {
                sleep(static::REFUND_RETRY_DELAY_SECONDS);
            }
        }

        $this->getLogger()->critical('Express checkout payment could not be refunded: payment webhook did not arrive.', [
            'expressCheckoutUuid' => $expressCheckoutUuid,
        ]);

        return false;
    }
}
