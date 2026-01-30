<?php

namespace Mollie\Yves\Mollie\Plugin\Webhook;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieRefundRequestTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
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
    protected const REFUND_ID_PREFIX = 're_';

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
        $refundId = $request->request->get('id', '');

        return str_starts_with($refundId, static::REFUND_ID_PREFIX);
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
        $refundId = $request->request->get('id');

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->setId($refundId);

        $mollieRefundRequestTransfer = (new MollieRefundRequestTransfer())
            ->setRefund($mollieRefundTransfer);

        $mollieRefundResponseTransfer = $this->getClient()->getPersistedRefundById($mollieRefundRequestTransfer);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefundId($refundId)
            ->setTransactionId($mollieRefundResponseTransfer->getRefund()->getTransactionId());

        $mollieRefundApiResponseTransfer = $this->getFactory()
            ->getMollieApiClient()
            ->getRefundByRefundId($mollieApiRequestTransfer);

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            return $this->createWebhookResponseTransfer(
                Response::HTTP_OK,
                $mollieRefundApiResponseTransfer->getMessage(),
            );
        }

        $this->getClient()->processRefundData($mollieRefundApiResponseTransfer->getMollieRefund());

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
}
