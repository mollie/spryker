<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Mollie\Shared\Mollie\MollieConfig as MollieConfigShared;
use SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class PaymentRedirectController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function paymentRedirectAction(Request $request): RedirectResponse
    {
        $orderReference = $request->query->get($this->getConfig()->getOrderReferenceQueryParamName());
        $key = sprintf('%s:%s', MollieConfigShared::MOLLIE_STORAGE_KEY_PREFIX, $orderReference);
        $paymentId = $this->getFactory()->getStorageClient()->get($key);

        if (!$paymentId) {
            return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_ERROR);
        }

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setPaymentId($paymentId);

        $payment = $this->getFactory()->getMollieApiClient()->getPayment($mollieApiRequestTransfer);

        if (in_array($payment->getStatus(), MollieConfigShared::MOLLIE_PAYMENT_STATUS_FAILED, true)) {
            $this->addErrorMessage(sprintf($this->getConfig()->getPaymentFailedMessage(), $payment->getStatus()));

            return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_ERROR);
        }

        return $this->redirectResponseInternal(CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SUCCESS);
    }
}
