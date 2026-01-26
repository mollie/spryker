<?php

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MollieRefundTransfer;

interface MollieRefundWriterInterface
{
    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return void
     */
    public function addMollieRefundData(int $idSalesOrder, MollieRefundTransfer $mollieRefundTransfer): void;
}
