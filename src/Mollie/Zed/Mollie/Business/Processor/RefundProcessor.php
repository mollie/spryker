<?php

namespace Mollie\Zed\Mollie\Business\Processor;

use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentMethodQueryParametersTransfer;
use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Zed\Mollie\Business\OrderItem\OrderItemGrossAmountCalculatorInterface;
use Mollie\Zed\Mollie\Business\Writer\MollieRefundWriterInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;

class RefundProcessor implements RefundProcessorInterface
{
    use LoggerTrait;

    /**
     * @param \Mollie\Zed\Mollie\Business\OrderItem\OrderItemGrossAmountCalculatorInterface $grossAmountCalculator
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Zed\Mollie\Business\Writer\MollieRefundWriterInterface $refundWriter
     */
    public function __construct(
        protected OrderItemGrossAmountCalculatorInterface $grossAmountCalculator,
        protected MollieRepositoryInterface $repository,
        protected MollieClientInterface $mollieClient,
        protected MollieRefundWriterInterface $refundWriter,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer
    {
        //testiraj order s vise itema, buildaj klasu za racunanje
        $orderItemsGrossAmount = $this->grossAmountCalculator->calculateOrderItemsGrossAmount($orderTransfer);

        // fetch payment id
        // dobi ga iz persistance layera
        $molliePaymentTransfer = $this->repository->getPaymentByOrderId($orderTransfer->getIdSalesOrder());

        // zovi endpoint

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

            // razmisli dal trebas varcati transfer, bez obzira na error uvijek mjenja omas status
            return $mollieRefundApiResponseTransfer;
        }

        // zapisi record u bazu i vrati mapirani transfer radi testova
        $this->refundWriter->addMollieRefundData($orderTransfer->getIdSalesOrder(), $mollieRefundApiResponseTransfer->getMollieRefund());

        return $mollieRefundApiResponseTransfer;
    }
}
