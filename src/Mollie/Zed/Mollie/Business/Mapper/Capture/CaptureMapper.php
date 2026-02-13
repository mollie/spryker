<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureCollectionTransfer;
use Generated\Shared\Transfer\MolliePaymentCaptureTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
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

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureCollectionTransfer
     */
    public function mapMollieCapturesArrayToMolliePaymentCaptureCollection(
        MolliePaymentTransfer $molliePaymentTransfer,
    ): MolliePaymentCaptureCollectionTransfer {
        $captureCollection = $molliePaymentTransfer->getEmbedded()['captures'] ?? [];
        $molliePaymentCaptureCollectionTransfer = new MolliePaymentCaptureCollectionTransfer();
        foreach ($captureCollection as $capture) {
            $molliePaymentCaptureTransfer = (new MolliePaymentCaptureTransfer())
                ->fromArray($capture, true);

            $molliePaymentCaptureTransfer
                ->setTransactionId($molliePaymentTransfer->getId());

            $molliePaymentCaptureCollectionTransfer->addCapture($molliePaymentCaptureTransfer);
        }

        return $molliePaymentCaptureCollectionTransfer;
    }
}
