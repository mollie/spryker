<?php


declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Controller;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
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

    public const string NO_APPLICABLE_WEBHOOK_HANDLERS_FOUND = 'No applicable webhook handlers found';

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

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setTransactionId($mollieResponseId);

        $molliePaymentApiResponseTransfer = $this->getClient()->getPaymentByTransactionId($mollieApiRequestTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
            return $this->createResponse(
                Response::HTTP_OK,
                $molliePaymentApiResponseTransfer->getMessage(),
            );
        }

        $webhookResponseTransfer = (new MollieWebhookResponseTransfer())
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage(static::NO_APPLICABLE_WEBHOOK_HANDLERS_FOUND);

        foreach ($this->getFactory()->getMollieWebhookHandlerPlugins() as $webhookHandlerPlugin) {
            if (!$webhookHandlerPlugin->isApplicable($molliePaymentApiResponseTransfer->getMolliePayment())) {
                continue;
            }

            $webhookResponseTransfer = $webhookHandlerPlugin->handle($molliePaymentApiResponseTransfer->getMolliePayment());
        }

        return $this->createResponse(
            $webhookResponseTransfer->getStatusCode(),
            $webhookResponseTransfer->getMessage(),
        );
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

    /**
     * @param int $statusCode
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function createWebhookResponseTransfer(int $statusCode, string $message): MollieWebhookResponseTransfer
    {
        return (new MollieWebhookResponseTransfer())
            ->setStatusCode($statusCode)
            ->setMessage($message);
    }
}
