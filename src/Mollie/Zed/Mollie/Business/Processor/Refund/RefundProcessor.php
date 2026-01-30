<?php

namespace Mollie\Zed\Mollie\Business\Processor\Refund;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface;
use Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface;
use Mollie\Zed\Mollie\Business\Writer\MollieRefundWriterInterface;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;

class RefundProcessor implements RefundProcessorInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Zed\Mollie\Business\Calculator\OrderItem\OrderItemGrossAmountCalculatorInterface $grossAmountCalculator
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Business\Writer\MollieRefundWriterInterface $refundWriter
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Business\Mapper\Oms\MolleOmsStatusMapperInterface $molleOmsStatusMapper
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface $omsFacade
     */
    public function __construct(
        protected OrderItemGrossAmountCalculatorInterface $grossAmountCalculator,
        protected MollieRepositoryInterface $repository,
        protected MollieClientInterface $mollieClient,
        protected MollieRefundWriterInterface $refundWriter,
        protected MollieEntityManagerInterface $entityManager,
        protected MolleOmsStatusMapperInterface $molleOmsStatusMapper,
        protected MollieToOmsInterface $omsFacade,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer
    {
        $orderItemsGrossAmount = $this->grossAmountCalculator->calculateOrderItemsGrossAmount($orderTransfer);

        $molliePaymentTransfer = $this->repository->getPaymentByOrderId($orderTransfer->getIdSalesOrder());

        $mollieAmount = (new MollieAmountTransfer())
            ->setValue($orderItemsGrossAmount)
            ->setCurrency($orderTransfer->getCurrencyIsoCode());

        $molliePaymentMethodQueryParameters = (new MolliePaymentMethodQueryParametersTransfer())
            ->setAmount($mollieAmount);

        $mollieApiRequestTransfer = (new MollieApiRequestTransfer())
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setMolliePaymentMethodQueryParameters($molliePaymentMethodQueryParameters)
            ->setDescription($molliePaymentTransfer->getDescription())
            ->setMetadata($molliePaymentTransfer->getMetadata());

        $mollieRefundApiResponseTransfer = $this->mollieClient->createRefund($mollieApiRequestTransfer);

        if (!$mollieRefundApiResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->error($mollieRefundApiResponseTransfer->getMessage());

            return $mollieRefundApiResponseTransfer;
        }

        $this->refundWriter->addMollieRefundData($orderTransfer, $molliePaymentTransfer, $mollieRefundApiResponseTransfer->getMollieRefund());

        return $mollieRefundApiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MollieRefundTransfer $mollieRefundTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = new MollieRefundResponseTransfer();

        $orderItems = $this->repository->getOrderItemsFromSpyMollieRefund($mollieRefundTransfer->getId());

        if (!$orderItems) {
            return $mollieRefundResponseTransfer->setIsSuccess(false);
        }

        $this->entityManager->updateMollieRefundWithStatus($mollieRefundTransfer);

        $omsEvent = $this->molleOmsStatusMapper->mapMollieStatusToOmsStatus($mollieRefundTransfer->getStatus());

        $this->omsFacade->triggerEvent($omsEvent, $orderItems, []);

        $mollieRefundResponseTransfer->setIsSuccess(true);

        return $mollieRefundResponseTransfer;
    }
}
