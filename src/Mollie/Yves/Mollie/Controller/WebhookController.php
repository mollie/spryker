<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Controller;

use Spryker\Shared\Log\LoggerTrait;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class WebhookController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function webhookAction(Request $request): Response
    {
        $mollieResponseId = $request->request->get('id', '');

        if (!$mollieResponseId) {
            return $this->createResponse(Response::HTTP_BAD_REQUEST, 'Missing ID parameter');
        }

        foreach ($this->getFactory()->getMollieWebhookHandlerPlugins() as $webhookHandlerPlugin) {
            if ($webhookHandlerPlugin->isApplicable($request)) {
                $webhookResponseTransfer = $webhookHandlerPlugin->handle($request);

                return $this->createResponse(
                    $webhookResponseTransfer->getStatusCode(),
                    $webhookResponseTransfer->getMessage(),
                );
            }
        }

        return $this->createResponse(Response::HTTP_BAD_REQUEST, 'No applicable webhook handler found');
    }

    /**
     * @param int $statusCode
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createResponse(int $statusCode, string $content): Response
    {
        return (new Response())
            ->setStatusCode($statusCode)
            ->setContent($content);
    }
}
