<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\Router;

use Spryker\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use Spryker\Yves\Router\Route\RouteCollection;

class MollieRouteProviderPlugin extends AbstractRouteProviderPlugin
{
    /**
     * @var string
     */
    public const ROUTE_MOLLIE_PAYMENT_REDIRECT = 'checkout/payment-redirect';

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addPaymentStatusRoute($routeCollection);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addPaymentStatusRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/checkout/payment-redirect', 'Mollie', 'PaymentRedirect', 'paymentRedirectAction');
        $route = $route->setMethods(['GET']);
        $routeCollection->add(static::ROUTE_MOLLIE_PAYMENT_REDIRECT, $route);

        return $routeCollection;
    }
}
