<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;

class CaptureMapper implements CaptureMapperInterface
{
     /**
      * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
      *
      * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer
      */
    public function mapPaymentCaptureToItemPaymentCaptureTransfer(
        MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer,
    ): MollieItemPaymentCaptureTransfer {
        return (new MollieItemPaymentCaptureTransfer())->fromArray($molliePaymentCaptureTransfer->toArray(), true);
    }
}
