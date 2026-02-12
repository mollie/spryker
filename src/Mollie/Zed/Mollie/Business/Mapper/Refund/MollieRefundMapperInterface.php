<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Refund;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundCollectionTransfer;
use Generated\Shared\Transfer\MollieRefundSaveTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;

interface MollieRefundMapperInterface
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
    ): MollieRefundSaveTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundCollectionTransfer
     */
    public function mapMolliePaymentRefundsToMollieRefundCollectionTransfer(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundCollectionTransfer;
}
