<?php

namespace Mollie\Zed\Mollie\Business\OrderItem;

interface OrderItemGrossAmountCalculatorInterface
{
    /**
     * @param array<int, object> $orderItems
     *
     * @return int
     */
    public function calculateOrderItemsGrossAmount(array $orderItems): int;
}
