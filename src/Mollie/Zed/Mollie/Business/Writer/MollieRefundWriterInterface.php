<?php

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface MollieRefundWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return void
     */
    public function addMollieRefundData(
        OrderTransfer $orderTransfer,
        MolliePaymentTransfer $molliePaymentTransfer,
        MollieRefundTransfer $mollieRefundTransfer,
    ): void;
}
