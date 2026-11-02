<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutPaymentRefund;

interface MollieExpressCheckoutPaymentRefundMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutPaymentRefund $spyMollieExpressCheckoutPaymentRefundEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutPaymentRefund
     */
    public function mapTransferToEntity(
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
        SpyMollieExpressCheckoutPaymentRefund $spyMollieExpressCheckoutPaymentRefundEntity,
    ): SpyMollieExpressCheckoutPaymentRefund;

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutPaymentRefund $spyMollieExpressCheckoutPaymentRefundEntity
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutPaymentRefundTransfer
     */
    public function mapEntityToTransfer(
        SpyMollieExpressCheckoutPaymentRefund $spyMollieExpressCheckoutPaymentRefundEntity,
        MollieExpressCheckoutPaymentRefundTransfer $mollieExpressCheckoutPaymentRefundTransfer,
    ): MollieExpressCheckoutPaymentRefundTransfer;
}
