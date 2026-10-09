<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Shared\Mollie\MollieConfig;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

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
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = $this->createMollieExpressCheckoutPaymentUpdateRequestTransfer(
            $molliePaymentTransfer,
        );

        $mollieExpressCheckoutPaymentUpdateResponseTransfer = $this->getClient()
            ->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);

        if (!$mollieExpressCheckoutPaymentUpdateResponseTransfer->getIsSuccessful()) {
            return $this->createWebhookResponseTransfer(
                Response::HTTP_NOT_FOUND,
                'Express checkout payment not found',
            );
        }

        return $this->createWebhookResponseTransfer(
            Response::HTTP_OK,
            'Express checkout payment webhook processed successfully',
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

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer
     */
    protected function createMollieExpressCheckoutPaymentUpdateRequestTransfer(
        MolliePaymentTransfer $molliePaymentTransfer,
    ): MollieExpressCheckoutPaymentUpdateRequestTransfer {
        $metadata = $molliePaymentTransfer->getMetadata();
        $expressCheckoutUuid = $metadata[MollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID];

        $mollieExpressCheckoutPaymentUpdateRequestTransfer = new MollieExpressCheckoutPaymentUpdateRequestTransfer();
        $mollieExpressCheckoutPaymentUpdateRequestTransfer
            ->setExpressCheckoutUuid($expressCheckoutUuid)
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus());

        return $mollieExpressCheckoutPaymentUpdateRequestTransfer;
    }
}
