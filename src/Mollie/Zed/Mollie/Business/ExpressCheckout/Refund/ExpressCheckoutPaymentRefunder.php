<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Shared\Mollie\MollieConfig as SharedMollieConfig;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Spryker\Shared\Log\LoggerTrait;

class ExpressCheckoutPaymentRefunder implements ExpressCheckoutPaymentRefunderInterface
{
    use LoggerTrait;

    /**
     * @see \Mollie\Zed\Mollie\Business\Handler\ExpressCheckoutMolliePaymentHandler::PENDING_PAYMENT_KEY_TRANSACTION_ID
     *
     * @var string
     */
    protected const PENDING_PAYMENT_KEY_TRANSACTION_ID = 'transactionId';

    /**
     * @var string
     */
    protected const REFUND_DESCRIPTION = 'Express checkout order could not be created';

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieToStorageClientInterface $storageClient,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutRefundResponseTransfer
     */
    public function refund(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutRefundResponseTransfer {
        $expressCheckoutUuid = $mollieExpressCheckoutPaymentUpdateRequestTransfer->getExpressCheckoutUuidOrFail();
        $pendingPayment = $this->storageClient->get($this->config->getExpressCheckoutPendingPaymentStorageKey($expressCheckoutUuid));
        $transactionId = is_array($pendingPayment) ? ($pendingPayment[static::PENDING_PAYMENT_KEY_TRANSACTION_ID] ?? null) : null;

        if (!$transactionId) {
            return (new MollieExpressCheckoutRefundResponseTransfer())
                ->setIsSuccessful(false)
                ->setIsPaymentKnown(false);
        }

        $paidAmountTransfer = $this->mollieClient
            ->getPaymentByTransactionId((new MollieApiRequestTransfer())->setTransactionId($transactionId))
            ->getMolliePayment()
            ?->getAmount();

        if (!$paidAmountTransfer) {
            return $this->createFailedResponse(sprintf('Payment %s could not be loaded from Mollie.', $transactionId));
        }

        $mollieRefundApiResponseTransfer = $this->mollieClient->createRefund(
            (new MollieApiRequestTransfer())
                ->setIdempotencyKey($expressCheckoutUuid)
                ->setRefund(
                    (new MollieRefundTransfer())
                        ->setTransactionId($transactionId)
                        ->setDescription(static::REFUND_DESCRIPTION)
                        ->setMetadata([SharedMollieConfig::EXPRESS_CHECKOUT_METADATA_KEY_UUID => $expressCheckoutUuid])
                        ->setAmount(
                            (new MollieAmountTransfer())
                                ->setCurrency($paidAmountTransfer->getCurrency())
                                ->setValue((string)$this->convertToMinorUnits((string)$paidAmountTransfer->getValue())),
                        ),
                ),
        );

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            return $this->createFailedResponse((string)$mollieRefundApiResponseTransfer->getMessage());
        }

        return (new MollieExpressCheckoutRefundResponseTransfer())
            ->setIsSuccessful(true)
            ->setIsPaymentKnown(true);
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
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutRefundResponseTransfer
     */
    protected function createFailedResponse(string $message): MollieExpressCheckoutRefundResponseTransfer
    {
        $this->getLogger()->critical('Express checkout payment could not be refunded.', ['message' => $message]);

        return (new MollieExpressCheckoutRefundResponseTransfer())
            ->setIsSuccessful(false)
            ->setIsPaymentKnown(true);
    }
}
