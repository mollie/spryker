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
     * @var string
     */
    public const ROUTE_MOLLIE_WEBHOOK = 'mollie/webhook';

    /**
     * @var string
     */
    public const ROUTE_MOLLIE_NEXT_GEN_WEBHOOK = 'mollie/next-gen/webhook';

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addPaymentStatusRoute($routeCollection);
        $routeCollection = $this->addWebhookRoute($routeCollection);
        $routeCollection = $this->addNextGenWebhookRoute($routeCollection);

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

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addWebhookRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/mollie/webhook', 'Mollie', 'Webhook', 'webhookAction');
        $route = $route->setMethods(['POST']);
        $routeCollection->add(static::ROUTE_MOLLIE_WEBHOOK, $route);

        return $routeCollection;
    }

     /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addNextGenWebhookRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/mollie/next-gen/webhook', 'Mollie', 'Webhook', 'nextGenWebhookAction');
        $route = $route->setMethods(['POST']);
        $routeCollection->add(static::ROUTE_MOLLIE_NEXT_GEN_WEBHOOK, $route);

        return $routeCollection;
    }
}
