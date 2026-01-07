<?php

namespace Mollie\Glue\MollieWebhookBackendApi\Plugin;

use Mollie\Glue\MollieWebhookBackendApi\Controller\MollieWebhookResourceController;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouteProviderPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class MollieRouteProviderPlugin extends AbstractPlugin implements RouteProviderPluginInterface
{
    /**
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addWebhookRoute($routeCollection);

        return $routeCollection;
    }

    protected function addWebhookRoute(RouteCollection $routeCollection): RouteCollection
    {
         $route = (new Route('/mollie/webhook'))
            ->setDefaults([
                '_controller' => [MollieWebhookResourceController::class, 'postWebhookAction'],
                '_resourceName' => 'MollieWebhook',
                '_method' => strtolower(Request::METHOD_POST),
            ])
            ->setMethods([Request::METHOD_POST]);

        $route->addDefaults(['scope' => 'user']);

        $routeCollection->add('mollie-webhook', $route);

        return $routeCollection;
    }
}
