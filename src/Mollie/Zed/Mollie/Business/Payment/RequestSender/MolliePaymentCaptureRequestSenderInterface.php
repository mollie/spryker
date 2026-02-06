<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Payment\RequestSender;

use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;

interface MolliePaymentCaptureRequestSenderInterface
{
    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function capturePayment(MolliePaymentCaptureRequestTransfer $molliePaymentCaptureRequestTransfer): MolliePaymentCaptureResponseTransfer;
}
