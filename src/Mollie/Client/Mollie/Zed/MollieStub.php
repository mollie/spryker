<?php

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MollieRefundRequestTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Generated\Shared\Transfer\MollieRefundTransfer;
use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class MollieStub implements MollieStubInterface
{
    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(protected ZedRequestClientInterface $zedStub)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundRequestTransfer $mollieRefundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function getPersistedRefundById(MollieRefundRequestTransfer $mollieRefundRequestTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = $this->zedStub->call('/mollie/gateway/get-refund-record', $mollieRefundRequestTransfer);

        return $mollieRefundResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        $updateOrderCollectionResponseTransfer = $this->zedStub->call('/mollie/gateway/update-order-collection', $updateOrderCollectionRequestTransfer);

        return $updateOrderCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MollieRefundTransfer $mollieRefundTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = $this->zedStub->call('/mollie/gateway/process-refund-data', $mollieRefundTransfer);

        return $mollieRefundResponseTransfer;
    }
}
