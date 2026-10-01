<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Widget;

use Mollie\Yves\Mollie\Plugin\Router\MollieRouteProviderPlugin;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Yves\Mollie\MollieConfig getConfig()
 */
class MollieExpressCheckoutWidget extends AbstractWidget
{
    public function __construct()
    {
        $this->addParameter(
            'expressCheckoutResolveEnabledMethodsEndpoint',
            MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_RESOLVE_ENABLED_METHODS,
        );
        $this->addParameter(
            'expressCheckoutCreateSessionEndpoint',
            MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_CREATE_SESSION,
        );
        $this->addParameter(
            'expressCheckoutPlaceOrderEndpoint',
            MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_PLACE_ORDER,
        );
        $this->addParameter('jsSrc', $this->getConfig()->getMollieExpressCheckoutJsSrc());
        $this->addParameter('locale', $this->getLocale());
        $this->addAddressFormParameters();
    }

    /**
     * @return void
     */
    protected function addAddressFormParameters(): void
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        $this->addParameter('addressForm', $this->getFactory()->getExpressCheckoutAddressForm($quoteTransfer)->createView());
        $this->addParameter('addressFormAction', MollieRouteProviderPlugin::ROUTE_PATH_MOLLIE_EXPRESS_CHECKOUT_SAVE_ADDRESSES);
        $this->addParameter('hasAddresses', $this->getFactory()->createExpressCheckoutAddressChecker()->hasAddresses($quoteTransfer));
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'MollieExpressCheckoutWidget';
    }

    /**
     * @api
     *
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@Mollie/views/mollie-express-checkout-widget/mollie-express-checkout-widget.twig';
    }
}
