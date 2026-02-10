<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieOrderItemPaymentCapture;

class MolliePaymentCaptureMapper implements MolliePaymentCaptureMapperInterface
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
    ): SpyMollieOrderItemPaymentCapture {
        return $mollieOrderItemPaymentCaptureEntity->fromArray($mollieItemPaymentCaptureTransfer->toArray());
    }
}
