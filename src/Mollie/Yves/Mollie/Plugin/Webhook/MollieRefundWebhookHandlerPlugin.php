<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class MollieRefundWebhookHandlerPlugin extends AbstractPlugin implements MollieWebhookHandlerPluginInterface
{
    /**
     * @var string
     */
    protected const REFUNDS = 'refunds';

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return bool
     */
    public function isApplicable(MolliePaymentTransfer $molliePaymentTransfer): bool
    {
        return array_key_exists(static::REFUNDS, $molliePaymentTransfer->getEmbedded());
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        $this->getClient()->processRefundData($molliePaymentTransfer);

        return $this->createWebhookResponseTransfer(
            Response::HTTP_OK,
            'Refund webhook processed successfully',
        );
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
