<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;

interface CaptureMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer
     */
    public function mapPaymentCaptureToItemPaymentCaptureTransfer(
        MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer,
    ): MollieItemPaymentCaptureTransfer;

   /**
    * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
    *
    * @return \Generated\Shared\Transfer\MolliePaymentCaptureCollectionTransfer
    */
    public function mapMollieCapturesArrayToMolliePaymentCaptureCollection(
        MolliePaymentTransfer $molliePaymentTransfer,
    ): MolliePaymentCaptureCollectionTransfer;
}
