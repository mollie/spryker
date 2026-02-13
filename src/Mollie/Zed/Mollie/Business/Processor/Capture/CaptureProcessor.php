<?php

namespace Mollie\Zed\Mollie\Business\Processor\Capture;

use Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer;
use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Zed\Mollie\Business\Mapper\Capture\CaptureMapperInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class CaptureProcessor implements CaptureProcessorInterface
{
    use TransactionTrait;

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $mollieRepository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $mollieEntityManager
     * @param \Mollie\Zed\Mollie\Business\Mapper\Capture\CaptureMapperInterface $captureMapper
     */
    public function __construct(
        protected MollieRepositoryInterface $mollieRepository,
        protected MollieEntityManagerInterface $mollieEntityManager,
        protected CaptureMapperInterface $captureMapper,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return \Generated\Shared\Transfer\MolliePaymentCaptureResponseTransfer
     */
    public function updatePaymentCaptureCollection(MolliePaymentTransfer $molliePaymentTransfer): MolliePaymentCaptureResponseTransfer
    {
        $molliePaymentCaptureResponseTransfer = new MolliePaymentCaptureResponseTransfer();
        $mollieItemPaymentCollectionTransfer = $this->captureMapper
            ->mapMolliePaymentToMollieItemPaymentCaptureCollection($molliePaymentTransfer);

        $this->getTransactionHandler()->handleTransaction(function () use ($mollieItemPaymentCollectionTransfer): void {
            foreach ($mollieItemPaymentCollectionTransfer->getCaptures() as $mollieItemPaymentCapture) {
                $this->mollieEntityManager->updateCapture($mollieItemPaymentCapture);
            }
        });

        return $molliePaymentCaptureResponseTransfer;
    }
}
