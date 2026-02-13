<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Processor\Capture;

use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;

interface CaptureProcessorInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer;
}
