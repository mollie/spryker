<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Webhook;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundRequesterInterface;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundStatusUpdaterInterface;
use Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ExpressCheckoutPaymentWebhookProcessor implements ExpressCheckoutPaymentWebhookProcessorInterface
{
    /**
     * @var string
     */
    protected const MESSAGE_PAYMENT_UPDATED = 'Express checkout payment webhook processed successfully';

    /**
     * @var string
     */
    protected const MESSAGE_PAYMENT_NOT_FOUND = 'Express checkout payment not found';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandlerInterface $expressCheckoutMolliePaymentHandler
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundRequesterInterface $expressCheckoutPaymentRefundRequester
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefundStatusUpdaterInterface $expressCheckoutPaymentRefundStatusUpdater
     */
    public function __construct(
        protected MollieRepositoryInterface $repository,
        protected ExpressCheckoutMolliePaymentHandlerInterface $expressCheckoutMolliePaymentHandler,
        protected ExpressCheckoutPaymentRefundRequesterInterface $expressCheckoutPaymentRefundRequester,
        protected ExpressCheckoutPaymentRefundStatusUpdaterInterface $expressCheckoutPaymentRefundStatusUpdater,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function process(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        $expressCheckoutUuid = $molliePaymentTransfer->getMetadata()[SharedMollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID];
        $mollieExpressCheckoutPaymentRefundTransfer = $this->repository->findExpressCheckoutPaymentRefundByExpressCheckoutUuid($expressCheckoutUuid);

        if ($mollieExpressCheckoutPaymentRefundTransfer !== null) {
            return $this->processPaymentRefund($molliePaymentTransfer, $mollieExpressCheckoutPaymentRefundTransfer);
        }

        return $this->updateOrderPayment($molliePaymentTransfer, $expressCheckoutUuid);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function processPaymentRefund(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieWebhookResponseTransfer {
        if ($mollieExpressCheckoutPaymentRefundTransfer->getRefundId() === null) {
            return $this->expressCheckoutPaymentRefundRequester->requestRefund($molliePaymentTransfer, $mollieExpressCheckoutPaymentRefundTransfer);
        }

        return $this->expressCheckoutPaymentRefundStatusUpdater->updateRefundStatus($molliePaymentTransfer, $mollieExpressCheckoutPaymentRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param string $expressCheckoutUuid
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function updateOrderPayment(MolliePaymentTransfer $molliePaymentTransfer, string $expressCheckoutUuid): MollieWebhookResponseTransfer
    {
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid($expressCheckoutUuid)
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setStatus($molliePaymentTransfer->getStatus());

        $mollieExpressCheckoutPaymentUpdateResponseTransfer = $this->expressCheckoutMolliePaymentHandler
            ->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);

        // The webhook can arrive before the order is saved. A non-2xx response makes Mollie send it again later.
        if (!$mollieExpressCheckoutPaymentUpdateResponseTransfer->getIsExpressPaymentFound()) {
            return (new MollieWebhookResponseTransfer())
                ->setStatusCode(Response::HTTP_NOT_FOUND)
                ->setMessage(static::MESSAGE_PAYMENT_NOT_FOUND);
        }

        return (new MollieWebhookResponseTransfer())
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage(static::MESSAGE_PAYMENT_UPDATED);
    }
}
