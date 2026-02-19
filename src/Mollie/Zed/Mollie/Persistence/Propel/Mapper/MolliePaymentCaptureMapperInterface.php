<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture;

interface MolliePaymentCaptureMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture $mollieOrderItemPaymentCaptureEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture
     */
    public function mapMollieOrderItemPaymentCaptureTransferToEntity(
        MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer,
        SpyMollieOrderItemPaymentCapture $mollieOrderItemPaymentCaptureEntity,
    ): SpyMollieOrderItemPaymentCapture;

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture $mollieOrderItemPaymentCaptureEntity
     *
     * @return \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer
     */
    public function mapFromSpyMollieOrderItemPaymentCaptureEntityToTransfer(
        SpyMollieOrderItemPaymentCapture $mollieOrderItemPaymentCaptureEntity,
    ): MollieItemPaymentCaptureTransfer;
}
