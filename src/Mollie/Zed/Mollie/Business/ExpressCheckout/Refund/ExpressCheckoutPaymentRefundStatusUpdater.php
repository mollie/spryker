<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\MollieWebhookResponseTransfer;
use Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Response;

class ExpressCheckoutPaymentRefundStatusUpdater implements ExpressCheckoutPaymentRefundStatusUpdaterInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const MOLLIE_REFUND_STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    protected const MESSAGE_REFUND_STATUS_UNCHANGED = 'Express checkout payment refund status is unchanged';

    /**
     * @var string
     */
    protected const MESSAGE_REFUND_STATUS_UPDATED = 'Express checkout payment refund status updated';

    /**
     * @var string
     */
    protected const MESSAGE_REFUND_FAILED = 'Express checkout payment refund failed, refund it manually.';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface $mollieRefundMapper
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
        protected MollieRefundMapperInterface $mollieRefundMapper,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieWebhookResponseTransfer
     */
    public function updateRefundStatus(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieWebhookResponseTransfer {
        $mollieRefundTransfer = $this->findMollieRefundById($molliePaymentTransfer, $mollieExpressCheckoutPaymentRefundTransfer->getRefundId());

        if ($mollieRefundTransfer === null) {
            return (new MollieWebhookResponseTransfer())
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage(static::MESSAGE_REFUND_STATUS_UNCHANGED);
        }

        $refundStatus = $mollieRefundTransfer->getStatus();
        $mollieExpressCheckoutPaymentRefundTransfer->setRefundStatus($refundStatus);
        $this->entityManager->updateExpressCheckoutPaymentRefund($mollieExpressCheckoutPaymentRefundTransfer);
        $this->updateOrderPaymentStatus($mollieExpressCheckoutPaymentRefundTransfer);

        if ($refundStatus === static::MOLLIE_REFUND_STATUS_FAILED) {
            $this->getLogger()->critical(static::MESSAGE_REFUND_FAILED, [
                'expressCheckoutUuid' => $mollieExpressCheckoutPaymentRefundTransfer->getExpressCheckoutUuid(),
                'transactionId' => $mollieExpressCheckoutPaymentRefundTransfer->getTransactionId(),
                'refundId' => $mollieExpressCheckoutPaymentRefundTransfer->getRefundId(),
            ]);
        }

        return (new MollieWebhookResponseTransfer())
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage(static::MESSAGE_REFUND_STATUS_UPDATED);
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
     * @param string $refundId
     *
     * @return \Generated\Shared\Transfer\MollieRefundTransfer|null
     */
    protected function findMollieRefundById(MolliePaymentTransfer $molliePaymentTransfer, string $refundId): ?MollieRefundTransfer
    {
        $mollieRefundCollectionTransfer = $this->mollieRefundMapper->mapMolliePaymentRefundsToMollieRefundCollectionTransfer($molliePaymentTransfer);

        foreach ($mollieRefundCollectionTransfer->getRefunds() as $mollieRefundTransfer) {
            if ($mollieRefundTransfer->getId() !== $refundId) {
                continue;
            }

            return $mollieRefundTransfer;
        }

        return null;
    }
}
