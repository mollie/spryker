<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Mollie\Yves\Mollie\MollieFactory getFactory()
 * @method \Mollie\Client\Mollie\MollieClient getClient()
 */
class MolliePaymentWebhookHandlerPlugin extends AbstractPlugin implements MollieWebhookHandlerPluginInterface
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

        return str_starts_with($paymentId, static::PAYMENT_ID_PREFIX);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        $orderCollectionRequestTransfer = $this->createOrderCollectionRequestTransfer(
            $molliePaymentTransfer,
        );

        $this->getClient()->updateOrderCollection($orderCollectionRequestTransfer);

        return $this->createWebhookResponseTransfer(
            Response::HTTP_OK,
            'Payment webhook processed successfully',
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
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    protected function createOrderCollectionRequestTransfer(
        MolliePaymentTransfer $molliePaymentTransfer,
    ): OrderCollectionRequestTransfer {
        $orderCollectionRequestTransfer = new OrderCollectionRequestTransfer();

        $orderCollectionRequestTransfer
            ->setId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus())
            ->setCaptureBefore($molliePaymentTransfer->getCaptureBefore());

        return $orderCollectionRequestTransfer;
    }
}
