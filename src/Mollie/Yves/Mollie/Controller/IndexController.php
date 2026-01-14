<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Spryker\Shared\Log\LoggerTrait;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(Request $request): Response
    {
        $this->getLogger()->info($request->getContent());
        $response = new Response('OK', 200);

        return $response;
    }
}
