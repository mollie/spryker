<?php


declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\MollieAmountTransfer;
use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Mollie\Client\Mollie\MollieClientInterface;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class MolliePaymentCaptureRequestSender implements MolliePaymentCaptureRequestSenderInterface
{
    use LoggerTrait;
    use TransactionTrait;

    /**
     * @param \Mollie\Client\Mollie\MollieClientInterface $mollieClient
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     */
    public function __construct(
        protected MollieClientInterface $mollieClient,
        protected MollieServiceInterface $mollieService,
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieEntityManagerInterface $mollieEntityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function capturePayment(
        MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer,
    ): MolliePaymentCaptureResponseTransfer {
        $molliePaymentCaptureResponseTransfer = (new MolliePaymentCaptureResponseTransfer())
            ->setIsSuccessful(true);

        $itemCollectionTransfer = $molliePaymentCaptureRequestTransfer->getItems();
        $orderTransfer = $molliePaymentCaptureRequestTransfer->getOrder();

        $mollieAmountTransfer = $this->getCaptureAmount($itemCollectionTransfer);
        $mollieAmountTransfer->setCurrency($orderTransfer->getCurrencyIsoCode());

        $molliePaymentTransfer = $this->mollieRepository->getPaymentByFkSalesOrder($orderTransfer->getIdSalesOrder());

        $molliePaymentCaptureTransfer = (new MolliePaymentCaptureTransfer())
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setAmount($mollieAmountTransfer)
            ->setDescription($this->getDescription($orderTransfer));

        $mollieApiRequestTransfer = new MollieApiRequestTransfer();
        $mollieApiRequestTransfer->setPaymentCapture($molliePaymentCaptureTransfer);
        $mollieCreateCaptureApiResponseTransfer = $this->mollieClient->createCapture($mollieApiRequestTransfer);

        if (!$mollieCreateCaptureApiResponseTransfer->getIsSuccessful()) {
             $molliePaymentCaptureResponseTransfer->setIsSuccessful(false);

             return $molliePaymentCaptureResponseTransfer;
        }

        $molliePaymentCaptureTransfer = $mollieCreateCaptureApiResponseTransfer->getPaymentCapture();
        $this->persistCapturePayment($itemCollectionTransfer, $molliePaymentCaptureTransfer);

        return $molliePaymentCaptureResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
     *
     * @return void
     */
    protected function persistCapturePayment(
        ItemCollectionTransfer $itemCollectionTransfer,
        MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer,
    ): void {
        $this->getTransactionHandler()->handleTransaction(function () use ($itemCollectionTransfer, $molliePaymentCaptureTransfer): void {
            foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
                $mollieItemPaymentCaptureTransfer = new MollieItemPaymentCaptureTransfer();
                $mollieItemPaymentCaptureTransfer->setFkSalesOrderItem($itemTransfer->getIdSalesOrderItem());
                $mollieAmountTransfer = $molliePaymentCaptureTransfer->getAmount();
                $mollieItemPaymentCaptureTransfer
                    ->setCurrency($mollieAmountTransfer->getCurrency())
                    ->setValue($mollieAmountTransfer->getValue());

                $mollieItemPaymentCaptureTransfer->fromArray($molliePaymentCaptureTransfer->toArray(), true);

                $this->mollieEntityManager->createCapture($mollieItemPaymentCaptureTransfer);
            }
        });
    }

    /**
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureTransfer
     */
    protected function buildMolliePaymentCaptureTransfer(): MolliePaymentCaptureTransfer
    {
        return new MolliePaymentCaptureTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MollieAmountTransfer
     */
    protected function getCaptureAmount(ItemCollectionTransfer $itemCollectionTransfer): MollieAmountTransfer
    {
        $captureAmount = 0;
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $captureAmount += $itemTransfer->getSumPriceToPayAggregation();
        }

        return $this->mollieService->convertIntegerToMollieAmount($captureAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string
     */
    protected function getDescription(OrderTransfer $orderTransfer): string
    {
        return sprintf('Capture for order: %s', $orderTransfer->getOrderReference());
    }
}
