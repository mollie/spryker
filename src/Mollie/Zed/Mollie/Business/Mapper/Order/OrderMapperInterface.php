<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Order;

use Generated\Shared\Transfer\OrderTransfer;

interface OrderMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<int, mixed> $orderItems
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function mapOrderItemsToOrderTransfer(OrderTransfer $orderTransfer, array $orderItems): OrderTransfer;
}
