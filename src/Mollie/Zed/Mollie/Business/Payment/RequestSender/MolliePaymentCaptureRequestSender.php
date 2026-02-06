<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;

class MolliePaymentCaptureRequestSender implements MolliePaymentCaptureRequestSenderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function capturePayment(MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer): MolliePaymentCaptureResponseTransfer
    {
        $itemCollectionTransfer = $molliePaymentCaptureRequestTransfer->getItems();
        $captureAmount = $this->getCaptureAmount($itemCollectionTransfer);

        return new MolliePaymentCaptureResponseTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemCollectionTransfer $itemCollectionTransfer
     *
     * @return int
     */
    protected function getCaptureAmount(ItemCollectionTransfer $itemCollectionTransfer): int
    {
        $captureAmount = 0;
        foreach ($itemCollectionTransfer->getItems() as $itemTransfer) {
            $captureAmount += $itemTransfer->getSumPriceToPayAggregation();
        }

        return $captureAmount;
    }
}
