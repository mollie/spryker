<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentLinkTransfer;
use Generated\Shared\Transfer\MollieWebhookEventTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Spryker\Client\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClientInterface getClient()
 */
class MolliePaymentLinkWebhookHandlerPlugin extends AbstractPlugin implements MollieNextGenWebhookHandlerPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieWebhookEventTransfer $mollieWebhookEventTransfer
     *
     * @return bool
     */
    public function isApplicable(MollieWebhookEventTransfer $mollieWebhookEventTransfer): bool
    {
        return $mollieWebhookEventTransfer->getType() === 'payment-link.paid';
    }

    /**
     * @param \Generated\Shared\Transfer\MollieWebhookEventTransfer $mollieWebhookEventTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MollieWebhookEventTransfer $mollieWebhookEventTransfer): MollieWebhookResponseTransfer
    {
        $embedded = $mollieWebhookEventTransfer->getEmbedded();

        $molliePaymentLinkTransfer = (new MolliePaymentLinkTransfer())
            ->fromArray($embedded['entity'] ?? [], true);

        $this->getClient()->updatePaymentLink($molliePaymentLinkTransfer);

        return (new MollieWebhookResponseTransfer())
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage('Payment webhook processed successfully');
    }
}
