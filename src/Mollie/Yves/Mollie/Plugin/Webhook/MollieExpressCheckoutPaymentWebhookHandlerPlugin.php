<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class MollieExpressCheckoutPaymentWebhookHandlerPlugin extends AbstractPlugin implements MollieWebhookHandlerPluginInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_ID_PREFIX = 'tr_';

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return bool
     */
    public function isApplicable(MolliePaymentTransfer $molliePaymentTransfer): bool
    {
        $paymentId = $molliePaymentTransfer->getId();
        if (!str_starts_with($paymentId, static::PAYMENT_ID_PREFIX)) {
            return false;
        }

        $metadata = $molliePaymentTransfer->getMetadata();
        $isExpressCheckoutPayment = array_key_exists(MollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID, $metadata);

        return $isExpressCheckoutPayment;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        return $this->getClient()->processExpressCheckoutPaymentWebhook($molliePaymentTransfer);
    }
}
