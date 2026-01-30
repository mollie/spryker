<?php

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MollieRefundRequestTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;

interface MollieStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieRefundRequestTransfer $mollieRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function getPersistedRefundById(MollieRefundRequestTransfer $mollieRefundRequestTransfer): MollieRefundResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionRequestTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MollieRefundTransfer $mollieRefundTransfer): MollieRefundResponseTransfer;
}
