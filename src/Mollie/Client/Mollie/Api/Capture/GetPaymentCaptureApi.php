<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Capture;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieGetCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class GetPaymentCaptureApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $molliePaymentCaptureTransfer = $mollieApiRequestTransfer->getPaymentCapture();

        $this->request = new GetPaymentCaptureRequest(
            paymentId: $molliePaymentCaptureTransfer->getTransactionId(),
            captureId: $molliePaymentCaptureTransfer->getId(),
        );

        return $this->request;
    }

    /**
     * @param \Generated\Shared\Transfer\MollieApiResponseTransfer $mollieApiResponseTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function mapApiResponse(MollieApiResponseTransfer $mollieApiResponseTransfer): AbstractTransfer
    {
        $mollieGetCaptureApiResponseTransfer = new MollieGetCaptureApiResponseTransfer();
        $mollieGetCaptureApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentCaptureTransfer = new MolliePaymentCaptureTransfer();
        $molliePaymentCaptureTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $mollieGetCaptureApiResponseTransfer->setPaymentCapture($molliePaymentCaptureTransfer);

        return $mollieGetCaptureApiResponseTransfer;
    }
}
