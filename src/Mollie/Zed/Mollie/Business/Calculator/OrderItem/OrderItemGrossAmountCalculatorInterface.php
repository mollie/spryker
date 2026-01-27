<?php

namespace Mollie\Zed\Mollie\Business\Calculator\OrderItem;

use Generated\Shared\Transfer\OrderTransfer;

interface OrderItemGrossAmountCalculatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    public function calculateOrderItemsGrossAmount(OrderTransfer $orderTransfer): int;
}
