<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder;

interface MollieExpressCheckoutFailedOrderMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity
     *
     * @return \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder
     */
    public function mapTransferToEntity(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
        SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity,
    ): SpyMollieExpressCheckoutFailedOrder;

    /**
     * @param \Orm\Zed\Mollie\Persistence\SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    public function mapEntityToTransfer(
        SpyMollieExpressCheckoutFailedOrder $spyMollieExpressCheckoutFailedOrderEntity,
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer;
}
