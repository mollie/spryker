<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Service\Mollie\MollieServiceInterface;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;
use Orm\Zed\Mollie\Persistence\SpyRefundMollie;

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
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function mapFromSpyRefundMollieEntityToMollieRefundTransfer(SpyRefundMollie $spyRefundMollieEntity): MollieRefundResponseTransfer
    {
        $mollieRefundTransfer = (new MollieRefundTransfer())
            ->fromArray($spyRefundMollieEntity->toArray(), true);

        return (new MollieRefundResponseTransfer())
            ->setRefund($mollieRefundTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundSaveTransfer $mollieRefundSaveTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyRefundMollie $spyRefundMollieEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyRefundMollie
     */
    public function mapToSpyRefundMollieEntity(
        MollieRefundSaveTransfer $mollieRefundSaveTransfer,
        SpyRefundMollie $spyRefundMollieEntity,
    ): SpyRefundMollie {
        $metadata = $this->utilEncodingService->encodeJson($mollieRefundSaveTransfer->getMetadata());

        $spyRefundMollieEntity
            ->setFkSalesOrderItem($mollieRefundSaveTransfer->getItem()->getIdSalesOrderItem())
            ->setDescription($mollieRefundSaveTransfer->getDescription())
            ->setCurrency($mollieRefundSaveTransfer->getCurrency())
            ->setValue($this->convertAmountToString($mollieRefundSaveTransfer->getItem()->getRefundableAmount()))
            ->setStatus($mollieRefundSaveTransfer->getStatus())
            ->setMetadata($metadata)
            ->setTransactionId($mollieRefundSaveTransfer->getTransactionId())
            ->setRefundId($mollieRefundSaveTransfer->getRefundId())
            ->setCreatedAt($mollieRefundSaveTransfer->getCreatedAt());

        return $spyRefundMollieEntity;
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    protected function convertAmountToString(int $amount): string
    {
        $mollieAmountTransfer = $this->mollieService->convertIntegerToMollieAmount($amount);

        return $mollieAmountTransfer->getValue();
    }
}
