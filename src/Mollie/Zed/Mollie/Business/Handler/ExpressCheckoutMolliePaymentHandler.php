<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer;
use Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface;
use Mollie\Zed\Mollie\MollieConfig;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class ExpressCheckoutMolliePaymentHandler implements ExpressCheckoutMolliePaymentHandlerInterface
{
    /**
     * @var string
     */
    protected const PENDING_PAYMENT_KEY_TRANSACTION_ID = 'transactionId';

    /**
     * @var string
     */
    protected const PENDING_PAYMENT_KEY_STATUS = 'status';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Dependency\MollieToStorageClientInterface $storageClient
     * @param \Mollie\Zed\Mollie\MollieConfig $config
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
        protected MollieToStorageClientInterface $storageClient,
        protected MollieConfig $config,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function createExpressCheckoutMolliePayment(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer,
    ): void {
        $saveOrderTransfer = $checkoutResponseTransfer->getSaveOrderOrFail();
        $idSalesOrder = $saveOrderTransfer->getIdSalesOrderOrFail();

        $paymentTransfer = $quoteTransfer->getPaymentOrFail();
        $mollieExpressPaymentTransfer = $paymentTransfer->getMollieExpressPaymentOrFail();
        $expressCheckoutUuid = $mollieExpressPaymentTransfer->getExpressCheckoutUuidOrFail();

        $this->entityManager->createExpressCheckoutMolliePayment($idSalesOrder, $expressCheckoutUuid);

        $this->applyPendingPayment($expressCheckoutUuid);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateResponseTransfer
     */
    public function updateExpressCheckoutMolliePayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): MollieExpressCheckoutPaymentUpdateResponseTransfer {
        $isUpdated = $this->entityManager->updateExpressCheckoutMolliePayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);

        if (!$isUpdated) {
            $this->rememberPendingPayment($mollieExpressCheckoutPaymentUpdateRequestTransfer);
        }

        $mollieExpressCheckoutPaymentUpdateResponseTransfer = new MollieExpressCheckoutPaymentUpdateResponseTransfer();
        $mollieExpressCheckoutPaymentUpdateResponseTransfer->setIsSuccessful($isUpdated);

        return $mollieExpressCheckoutPaymentUpdateResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer
     *
     * @return void
     */
    protected function rememberPendingPayment(
        MollieExpressCheckoutPaymentUpdateRequestTransfer $mollieExpressCheckoutPaymentUpdateRequestTransfer,
    ): void {
        $this->storageClient->set(
            $this->config->getExpressCheckoutPendingPaymentStorageKey(
                $mollieExpressCheckoutPaymentUpdateRequestTransfer->getExpressCheckoutUuidOrFail(),
            ),
            (string)json_encode([
                static::PENDING_PAYMENT_KEY_TRANSACTION_ID => $mollieExpressCheckoutPaymentUpdateRequestTransfer->getTransactionIdOrFail(),
                static::PENDING_PAYMENT_KEY_STATUS => $mollieExpressCheckoutPaymentUpdateRequestTransfer->getStatusOrFail(),
            ]),
            $this->config->getExpressCheckoutPendingPaymentStorageTtl(),
        );
    }

    /**
     * @param string $expressCheckoutUuid
     *
     * @return void
     */
    protected function applyPendingPayment(string $expressCheckoutUuid): void
    {
        $storageKey = $this->config->getExpressCheckoutPendingPaymentStorageKey($expressCheckoutUuid);
        $pendingPayment = $this->storageClient->get($storageKey);

        if (!is_array($pendingPayment) || empty($pendingPayment[static::PENDING_PAYMENT_KEY_TRANSACTION_ID])) {
            return;
        }

        $this->entityManager->updateExpressCheckoutMolliePayment(
            (new MollieExpressCheckoutPaymentUpdateRequestTransfer())
                ->setExpressCheckoutUuid($expressCheckoutUuid)
                ->setTransactionId($pendingPayment[static::PENDING_PAYMENT_KEY_TRANSACTION_ID])
                ->setStatus($pendingPayment[static::PENDING_PAYMENT_KEY_STATUS]),
        );
        $this->storageClient->delete($storageKey);
    }
}
