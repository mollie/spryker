<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;

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
}
