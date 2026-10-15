<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Shared\Mollie\MollieConstants;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Response;

class ExpressCheckoutPaymentRefundRequester implements ExpressCheckoutPaymentRefundRequesterInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const MESSAGE_PAYMENT_NOT_PAID = 'Express checkout payment is not paid, nothing to refund';

    /**
     * @var string
     */
    protected const MESSAGE_REFUND_NOT_CREATED = 'Express checkout payment refund could not be created';

    /**
     * @var string
     */
    protected const MESSAGE_REFUND_REQUESTED = 'Express checkout payment refund requested';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Business\ExpressCheckout\Refund\ExpressCheckoutPaymentRefunderInterface $expressCheckoutPaymentRefunder
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
        protected ExpressCheckoutPaymentRefunderInterface $expressCheckoutPaymentRefunder,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function requestRefund(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieWebhookResponseTransfer {
        $mollieExpressCheckoutPaymentRefundTransfer = $this->mapMolliePaymentTransferToMollieExpressCheckoutPaymentRefundTransfer(
            $molliePaymentTransfer,
            $mollieExpressCheckoutPaymentRefundTransfer,
        );
        $mollieExpressCheckoutPaymentRefundTransfer = $this->entityManager->updateExpressCheckoutPaymentRefund($mollieExpressCheckoutPaymentRefundTransfer);

        if ($mollieExpressCheckoutPaymentRefundTransfer->getPaymentStatus() !== MollieConstants::STATUS_PAID) {
            return (new MollieWebhookResponseTransfer())
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage(static::MESSAGE_PAYMENT_NOT_PAID);
        }

        $mollieRefundApiResponseTransfer = $this->expressCheckoutPaymentRefunder->refund($mollieExpressCheckoutPaymentRefundTransfer);

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->critical(static::MESSAGE_REFUND_NOT_CREATED, [
                'expressCheckoutUuid' => $mollieExpressCheckoutPaymentRefundTransfer->getExpressCheckoutUuid(),
                'transactionId' => $mollieExpressCheckoutPaymentRefundTransfer->getTransactionId(),
                'message' => $mollieRefundApiResponseTransfer->getMessage(),
            ]);

            return (new MollieWebhookResponseTransfer())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage(static::MESSAGE_REFUND_NOT_CREATED);
        }

        $mollieRefundTransfer = $mollieRefundApiResponseTransfer->getMollieRefund();

        $mollieExpressCheckoutPaymentRefundTransfer
            ->setRefundId($mollieRefundTransfer->getId())
            ->setRefundStatus($mollieRefundTransfer->getStatus());

        $this->entityManager->updateExpressCheckoutPaymentRefund($mollieExpressCheckoutPaymentRefundTransfer);
        $this->updateOrderPaymentStatus($mollieExpressCheckoutPaymentRefundTransfer);

        return (new MollieWebhookResponseTransfer())
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage(static::MESSAGE_REFUND_REQUESTED);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return void
     */
    protected function updateOrderPaymentStatus(MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer): void
    {
        $mollieExpressCheckoutPaymentUpdateRequestTransfer = (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
            ->setExpressCheckoutUuid($mollieExpressCheckoutPaymentRefundTransfer->getExpressCheckoutUuid())
            ->setTransactionId($mollieExpressCheckoutPaymentRefundTransfer->getTransactionId())
            ->setStatus($mollieExpressCheckoutPaymentRefundTransfer->getRefundStatus());

        $this->entityManager->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    protected function mapMolliePaymentTransferToMollieExpressCheckoutPaymentRefundTransfer(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieExpressCheckoutPaymentRefundTransfer {
        $mollieAmountTransfer = $molliePaymentTransfer->getAmount();
        $paidAmount = $this->mollieService->convertMollieAmountToInteger($mollieAmountTransfer);

        return $mollieExpressCheckoutPaymentRefundTransfer
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setPaymentStatus($molliePaymentTransfer->getStatus())
            ->setAmount($paidAmount)
            ->setCurrency($mollieAmountTransfer->getCurrency());
    }
}
