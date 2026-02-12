<?php

namespace Mollie\Zed\Mollie\Business\Processor\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface;
use Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface;
use Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class RefundProcessor implements RefundProcessorInterface
{
    use LoggerTrait;
    use TransactionTrait;

    /**
     * @param \Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface $grossAmountCalculator
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface $molleOmsStatusMapper
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface $omsFacade
     * @param \Mollie\Zed\Mollie\Business\Mapper\Refund\MollieRefundMapperInterface $refundMapper
     */
    public function __construct(
        protected OrderItemGrossAmountCalculatorInterface $grossAmountCalculator,
        protected MollieRepositoryInterface $repository,
        protected MollieClientInterface $mollieClient,
        protected MollieEntityManagerInterface $entityManager,
        protected MolleOmsStatusMapperInterface $molleOmsStatusMapper,
        protected MollieToOmsInterface $omsFacade,
        protected MollieRefundMapperInterface $refundMapper,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer
    {
        $orderItemsGrossAmount = $this->grossAmountCalculator->calculateTotalRefundableAmount($orderTransfer);

        $molliePaymentTransfer = $this->repository->getPaymentByFkSalesOrder($orderTransfer->getIdSalesOrder());

        $mollieAmount = (new MollieAmountTransfer())
            ->setValue($orderItemsGrossAmount)
            ->setCurrency($orderTransfer->getCurrencyIsoCode());

        $refundTransfer = (new MollieRefundTransfer())
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setAmount($mollieAmount)
            ->setDescription($molliePaymentTransfer->getDescription())
            ->setMetadata([$molliePaymentTransfer->getMetadata()]);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setRefund($refundTransfer);

        $mollieRefundApiResponseTransfer = $this->mollieClient->createRefund($mollieApiRequestTransfer);

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->error($mollieRefundApiResponseTransfer->getMessage());

            return $mollieRefundApiResponseTransfer;
        }

        $mollieRefundSaveTransfer = $this->refundMapper->mapRefundDataToMollieRefundSaveTransfer($molliePaymentTransfer, $mollieRefundApiResponseTransfer->getMollieRefund());

        $this->getTransactionHandler()->handleTransaction(function () use ($mollieRefundSaveTransfer, $orderTransfer): void {
            foreach ($orderTransfer->getItems() as $itemTransfer) {
                $mollieRefundSaveTransfer->setItem($itemTransfer);

                $this->entityManager->createRefund($mollieRefundSaveTransfer);
            }
        });

        return $mollieRefundApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = new MollieRefundResponseTransfer();

        $mollieRefundCollectionTransfer = $this->refundMapper->mapMolliePaymentRefundsToMollieRefundCollectionTransfer($molliePaymentTransfer);
        $this->entityManager->updateMollieRefundWithStatus($mollieRefundCollectionTransfer);

        $mollieRefundResponseTransfer->setIsSuccess(true);

        return $mollieRefundResponseTransfer;
    }
}
