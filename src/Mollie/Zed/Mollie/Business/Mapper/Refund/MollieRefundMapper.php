<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Refund;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;

class MollieRefundMapper implements MollieRefundMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundSaveTransfer
     */
    public function mapRefundDataToMollieRefundSaveTransfer(
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieRefundTransfer $mollieRefundTransfer,
    ): MollieRefundSaveTransfer {
        return (new MollieRefundSaveTransfer())
            ->setDescription($mollieRefundTransfer->getDescription())
            ->setCurrency($mollieRefundTransfer->getAmount()->getCurrency())
            ->setValue($mollieRefundTransfer->getAmount()->getValue())
            ->setStatus($mollieRefundTransfer->getStatus())
            ->setMetadata($mollieRefundTransfer->getMetadata())
            ->setTransactionId($molliePaymentTransfer->getId())
            ->setRefundId($mollieRefundTransfer->getId())
            ->setCreatedAt($mollieRefundTransfer->getCreatedAt());
    }
}
