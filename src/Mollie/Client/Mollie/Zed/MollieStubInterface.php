<?php

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;

interface MollieStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer;
}
