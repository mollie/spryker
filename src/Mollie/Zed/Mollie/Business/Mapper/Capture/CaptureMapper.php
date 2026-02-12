<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface;

class CaptureMapper implements CaptureMapperInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Dependency\Service\MollieToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(protected MollieToUtilEncodingServiceInterface $utilEncodingService)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer
     */
    public function mapPaymentCaptureToItemPaymentCaptureTransfer(
        MolliePaymentCaptureTransfer $molliePaymentCaptureTransfer,
    ): MollieItemPaymentCaptureTransfer {
        $mollieItemPaymentCaptureTransfer = (new MollieItemPaymentCaptureTransfer())
            ->fromArray($molliePaymentCaptureTransfer->toArray(), true);

        $metadata = $this->utilEncodingService->encodeJson($molliePaymentCaptureTransfer->getMetadata());

        $mollieItemPaymentCaptureTransfer
            ->setCaptureId($molliePaymentCaptureTransfer->getId())
            ->setMetadata($metadata);

        return $mollieItemPaymentCaptureTransfer;
    }
}
