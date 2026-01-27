<?php

namespace Mollie\Zed\Mollie\Business\Processor\Refund;

use Generated\Shared\Transfer\MollieRefundApiResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface RefundProcessorInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Business\Processor\Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundApiResponseTransfer
     */
    public function processOrderItemsRefund(OrderTransfer $orderTransfer): MollieRefundApiResponseTransfer;
}
