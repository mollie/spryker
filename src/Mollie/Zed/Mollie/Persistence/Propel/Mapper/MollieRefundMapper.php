<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;
use Propel\Runtime\Collection\ObjectCollection;

class MollieRefundMapper implements MollieRefundMapperInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     * @param \Mollie\Service\Mollie\MollieServiceInterface $mollieService
     */
    public function __construct(
        protected MollieToUtilEncodingServiceInterface $utilEncodingService,
        protected MollieServiceInterface $mollieService,
    ) {
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $spyRefundMollieEntity
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function mapFromSpyRefundMollieEntityToMollieRefundTransfer(ObjectCollection $spyRefundMollieEntity): MollieRefundResponseTransfer
    {
        $spyRefundMollieRecord = null;

        foreach ($spyRefundMollieEntity->getData() as $spyRefundMollie) {
            $spyRefundMollieRecord = $spyRefundMollie;
        }

        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->fromArray($spyRefundMollieRecord->toArray(), true);

        return (new MollieRefundResponseTransfer())
            ->setRefund($mollieRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function mapToSpyRefundMollieEntity(
        ItemTransfer $itemTransfer,
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieRefundTransfer $mollieRefundTransfer,
        SpyRefundMollie $spyRefundMollieEntity,
    ): SpyRefundMollie {
        $metadata = $this->utilEncodingService->encodeJson($mollieRefundTransfer->getMetadata());

        $spyRefundMollieEntity
            ->setFkSalesOrderItem($itemTransfer->getIdSalesOrderItem())
            ->setDescription($mollieRefundTransfer->getDescription())
            ->setCurrency($mollieRefundTransfer->getAmount()->getCurrency())
            ->setValue($this->convertAmountToString($itemTransfer->getRefundableAmount()))
            ->setStatus($mollieRefundTransfer->getStatus())
            ->setMetadata($metadata)
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setRefundId($mollieRefundTransfer->getId())
            ->setCreatedAt($mollieRefundTransfer->getCreatedAt());

        return $spyRefundMollieEntity;
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    protected function convertAmountToString(int $amount): string
    {
        $amount = $this->mollieService->convertIntegerToDecimal($amount);
        $amount = number_format($amount, 2, '.', '');

        return $amount;
    }
}
