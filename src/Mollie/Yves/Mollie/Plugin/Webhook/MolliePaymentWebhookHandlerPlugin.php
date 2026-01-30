<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentApiResponseTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
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
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function isApplicable(Request $request): bool
    {
        $paymentId = $request->request->get('id', '');

        return str_starts_with($paymentId, static::PAYMENT_ID_PREFIX);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function handle(Request $request): MollieWebhookResponseTransfer
    {
        $paymentId = $request->request->get('id');

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setTransactionId($paymentId);

        $molliePaymentApiResponseTransfer = $this->getFactory()
            ->getMollieApiClient()
            ->getPaymentByTransactionId($mollieApiRequestTransfer);

        if (!$molliePaymentApiResponseTransfer->getIsSuccessful()) {
            return $this->createWebhookResponseTransfer(
                Response::HTTP_OK,
                $molliePaymentApiResponseTransfer->getMessage(),
            );
        }

        $orderCollectionRequestTransfer = $this->createOrderCollectionRequestTransfer(
            $molliePaymentApiResponseTransfer,
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
     * @param \Generated\Shared\Transfer\MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    protected function createOrderCollectionRequestTransfer(
        MolliePaymentApiResponseTransfer $molliePaymentApiResponseTransfer,
    ): OrderCollectionRequestTransfer {
        $orderCollectionRequestTransfer = new OrderCollectionRequestTransfer();
        $molliePaymentTransfer = $molliePaymentApiResponseTransfer->getMolliePayment();
        $orderCollectionRequestTransfer
            ->setId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus());

        return $orderCollectionRequestTransfer;
    }
}
