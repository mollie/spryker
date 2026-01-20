<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use ArrayObject;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Shared\Mollie\MollieConfig as MollieConfigShared;
use SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentRedirectController extends AbstractMollieController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function paymentRedirectAction(Request $request): RedirectResponse
    {
        $orderReference = $request->query->get($this->getFactory()->getConfig()->getOrderReferenceQueryParamName());
        $key = sprintf('%s:%s', MollieConfigShared::MOLLIE_PAYMENT_TRANSACTION_STORAGE_KEY_PREFIX, $orderReference);
        $paymentId = $this->getFactory()->getStorageClient()->get($key);

        if (!$paymentId) {
            $this->addErrorMessage($this->getFactory()->getConfig()->getPaymentIdDoesntExistMessage());
            $this->clearOrderReference();

            return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PAYMENT);
        }

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setTransactionId($paymentId);

        $molliePaymentApiResponseTransfer = $this->getFactory()->getMollieApiClient()->getPaymentByTransactionId($mollieApiRequestTransfer);
        $payment = $molliePaymentApiResponseTransfer->getMolliePayment();

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful() || in_array($payment->getStatus(), MollieConfigShared::MOLLIE_PAYMENT_STATUS_FAILED, true)) {
            $this->addErrorMessage(sprintf($this->getFactory()->getConfig()->getPaymentFailedMessage(), $payment->getStatus()));
            $this->clearOrderReference();

            return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_PAYMENT);
        }

        return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SUCCESS);
    }

    /**
     * @return void
     */
    protected function clearOrderReference(): void
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();
        $quoteTransfer->setOrderReference(null)
            ->setErrors(new ArrayObject());

        $this->getFactory()->getQuoteClient()->setQuote($quoteTransfer);
    }
}
