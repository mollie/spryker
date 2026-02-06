<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie\Api\Capture;

use Generated\Shared\Transfer\MollieApiRequestTransfer;
use Generated\Shared\Transfer\MollieApiResponseTransfer;
use Generated\Shared\Transfer\MollieCreateCaptureApiResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Client\Mollie\Api\AbstractApiCall;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class CreatePaymentCaptureApi extends AbstractApiCall
{
    /**
     * @param \Generated\Shared\Transfer\MollieApiRequestTransfer|null $mollieApiRequestTransfer
     *
     * @return \Mollie\Api\Http\Request|null
     */
    protected function buildRequest(?MollieApiRequestTransfer $mollieApiRequestTransfer = null): ?Request
    {
        $mollieCapturePayment = $mollieApiRequestTransfer->getPaymentCapture();

        $this->request = new CreatePaymentCaptureRequest(
            paymentId: $mollieCapturePayment->getPaymentId(),
            description: sprintf('Capture for: %s', $mollieCapturePayment->getPaymentId()),
            metadata: [
                'bookkeeping_id' => $mollieCapturePayment->getPaymentId(),
            ],
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
        $mollieCreateCaptureApiResponseTransfer = new MollieCreateCaptureApiResponseTransfer();
        $mollieCreateCaptureApiResponseTransfer
            ->setIsSuccessful($mollieApiResponseTransfer->getIsSuccessful())
            ->setMessage($mollieApiResponseTransfer->getMessage());

        $molliePaymentCaptureTransfer = new MolliePaymentCaptureTransfer();
        $molliePaymentCaptureTransfer->fromArray($mollieApiResponseTransfer->getPayload(), true);

        $mollieCreateCaptureApiResponseTransfer->setPaymentCapture($molliePaymentCaptureTransfer);

        return $mollieCreateCaptureApiResponseTransfer;
    }
}
