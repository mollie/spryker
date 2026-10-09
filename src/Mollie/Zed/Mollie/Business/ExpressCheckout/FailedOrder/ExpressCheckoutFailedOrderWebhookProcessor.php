<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Response;

class ExpressCheckoutFailedOrderWebhookProcessor implements ExpressCheckoutFailedOrderWebhookProcessorInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface $paymentRefunder
     */
    public function __construct(
        protected MollieRepositoryInterface $repository,
        protected MollieEntityManagerInterface $entityManager,
        protected ExpressCheckoutPaymentRefunderInterface $paymentRefunder,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function process(MolliePaymentTransfer $molliePaymentTransfer): MollieWebhookResponseTransfer
    {
        $expressCheckoutUuid = $molliePaymentTransfer->getMetadata()[SharedMollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID] ?? null;

        if (!$expressCheckoutUuid) {
            return $this->createNotHandledResponse();
        }

        $mollieExpressCheckoutFailedOrderTransfer = $this->repository->findExpressCheckoutFailedOrderByExpressCheckoutUuid($expressCheckoutUuid);

        if (!$mollieExpressCheckoutFailedOrderTransfer) {
            return $this->createNotHandledResponse();
        }

        // The order exists after all (e.g. a post-save step failed): never refund a payment that belongs to an order.
        if ($this->repository->hasMolliePaymentByExpressCheckoutUuid($expressCheckoutUuid)) {
            $this->getLogger()->critical('Express checkout payment has a failed order record and an order; it is not refunded.', [
                'expressCheckoutUuid' => $expressCheckoutUuid,
                'transactionId' => $molliePaymentTransfer->getId(),
            ]);

            return $this->createNotHandledResponse();
        }

        $mollieExpressCheckoutFailedOrderTransfer = $this->entityManager->updateExpressCheckoutFailedOrder(
            $this->mapPaymentToFailedOrder($molliePaymentTransfer, $mollieExpressCheckoutFailedOrderTransfer),
        );

        if ($mollieExpressCheckoutFailedOrderTransfer->getRefundId()) {
            return $this->createHandledResponse(Response::HTTP_OK, 'Express checkout payment is already refunded');
        }

        if ($mollieExpressCheckoutFailedOrderTransfer->getStatus() !== MollieConstants::STATUS_PAID) {
            return $this->createHandledResponse(Response::HTTP_OK, 'Express checkout payment is not paid, nothing to refund');
        }

        return $this->refund($mollieExpressCheckoutFailedOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function refund(MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer): MollieWebhookResponseTransfer
    {
        $mollieRefundApiResponseTransfer = $this->paymentRefunder->refund($mollieExpressCheckoutFailedOrderTransfer);

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->critical('Express checkout payment could not be refunded.', [
                'expressCheckoutUuid' => $mollieExpressCheckoutFailedOrderTransfer->getExpressCheckoutUuid(),
                'transactionId' => $mollieExpressCheckoutFailedOrderTransfer->getTransactionId(),
                'message' => $mollieRefundApiResponseTransfer->getMessage(),
            ]);

            // Not 2xx, so Mollie calls the webhook again and the refund is retried.
            return $this->createHandledResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Express checkout payment could not be refunded');
        }

        $this->entityManager->updateExpressCheckoutFailedOrder(
            $mollieExpressCheckoutFailedOrderTransfer->setRefundId($mollieRefundApiResponseTransfer->getMollieRefundOrFail()->getId()),
        );

        return $this->createHandledResponse(Response::HTTP_OK, 'Express checkout payment refunded');
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    protected function mapPaymentToFailedOrder(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer {
        $mollieAmountTransfer = $molliePaymentTransfer->getAmountOrFail();

        return $mollieExpressCheckoutFailedOrderTransfer
            ->setTransactionId($molliePaymentTransfer->getIdOrFail())
            ->setStatus($molliePaymentTransfer->getStatusOrFail())
            ->setAmount($this->convertToMinorUnits((string)$mollieAmountTransfer->getValue()))
            ->setCurrency($mollieAmountTransfer->getCurrency());
    }

    /**
     * @param string $decimalAmount
     *
     * @return int
     */
    protected function convertToMinorUnits(string $decimalAmount): int
    {
        return (int)round((float)$decimalAmount * 100);
    }

    /**
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function createNotHandledResponse(): MollieWebhookResponseTransfer
    {
        return (new MollieWebhookResponseTransfer())->setIsHandled(false);
    }

    /**
     * @param int $statusCode
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    protected function createHandledResponse(int $statusCode, string $message): MollieWebhookResponseTransfer
    {
        return (new MollieWebhookResponseTransfer())
            ->setIsHandled(true)
            ->setStatusCode($statusCode)
            ->setMessage($message);
    }
}
