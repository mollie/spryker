<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Capture;

use Generated\Shared\Transfer\MollieItemPaymentCaptureCollectionTransfer;
use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
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
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureCollectionTransfer
     */
    public function mapMolliePaymentToMollieItemPaymentCaptureCollection(
        MolliePaymentTransfer $molliePaymentTransfer,
    ): MollieItemPaymentCaptureCollectionTransfer {
        $captureCollection = $molliePaymentTransfer->getEmbedded()['captures'] ?? [];
        $itemPaymentCaptureCollectionTransfer = new MollieItemPaymentCaptureCollectionTransfer();
        foreach ($captureCollection as $capture) {
            $itemPaymentCaptureTransfer = (new MollieItemPaymentCaptureTransfer())
                ->fromArray($capture, true);

            $itemPaymentCaptureTransfer
                ->setCaptureId($capture['id'])
                ->setTransactionId($molliePaymentTransfer->getId());

            $itemPaymentCaptureCollectionTransfer->addCapture($itemPaymentCaptureTransfer);
        }

        return $itemPaymentCaptureCollectionTransfer;
    }
}
