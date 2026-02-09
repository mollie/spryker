<?php

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer;
use Orm\Zed\Mollie\Persistence\SpyMolliePaymentCapture;

interface MolliePaymentCaptureMapperInterface
{
     /**
      * @param \Generated\Shared\Transfer\MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer
      * @param \Orm\Zed\Mollie\Persistence\SpyMolliePaymentCapture $molliePaymentCaptureEntity
      *
      * @return \Orm\Zed\Mollie\Persistence\SpyMolliePaymentCapture
      */
    public function mapMollieOrderItemPaymentCaptureTransferToEntity(
        MollieItemPaymentCaptureTransfer $mollieItemPaymentCaptureTransfer,
        SpyMolliePaymentCapture $molliePaymentCaptureEntity,
    ): SpyMolliePaymentCapture;
}
