<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

class MollieCaptureWebhookHandlerPlugin extends AbstractPlugin implements MollieWebhookHandlerPluginInterface
{
    /**
     * @var string
     */
    protected const CAPTURES = 'captures';

    public function isApplicable(MolliePaymentTransfer $molliePaymentTransfer): bool
    {
         return array_key_exists(static::CAPTURES, $molliePaymentTransfer->getEmbedded());
    }

    public function handle(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        // TODO: Implement handle() method.
    }
}
