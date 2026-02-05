<?php

namespace Mollie\Zed\Mollie\Business\Calculator\OrderItem;

use Generated\Shared\Transfer\OrderTransfer;

class OrderItemGrossAmountCalculator implements OrderItemGrossAmountCalculatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    public function calculateTotalRefundableAmount(OrderTransfer $orderTransfer): int
    {
        $refundableAmount = 0;

        foreach ($orderTransfer->getItems() as $orderItem) {
            $refundableAmount += $orderItem->getRefundableAmount();
        }

        return $refundableAmount;
    }
}
