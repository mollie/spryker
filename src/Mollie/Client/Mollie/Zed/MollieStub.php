<?php

namespace Mollie\Client\Mollie\Zed;

use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
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
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function processRefundData(MolliePaymentTransfer $molliePaymentTransfer): MollieRefundResponseTransfer
    {
        $mollieRefundResponseTransfer = $this->zedStub->call('/mollie/gateway/process-refund-data', $molliePaymentTransfer);

        return $mollieRefundResponseTransfer;
    }

     /**
      * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
      *
      * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
      */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer $molliePaymentCaptureResponseTransfer */
        $molliePaymentCaptureResponseTransfer = $this->zedStub
            ->call('/mollie/gateway/update-payment-capture-collection', $molliePaymentTransfer);

        return $molliePaymentCaptureResponseTransfer;
    }
}
