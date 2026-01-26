<?php

namespace Mollie\Zed\Mollie\Business\OrderItem;

class OrderItemGrossAmountCalculator implements OrderItemGrossAmountCalculatorInterface
{
    /**
     * @param array<int, object> $orderItems
     *
     * @return int
     */
    public function calculateOrderItemsGrossAmount(array $orderItems): int
    {
        $refundableAmount = null;

        foreach ($orderItems as $orderItem) {
            $refundableAmount += $orderItem->getRefundableAmount();
        }

        return $refundableAmount;
    }
}
