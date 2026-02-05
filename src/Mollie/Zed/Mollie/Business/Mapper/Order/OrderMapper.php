<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Order;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class OrderMapper implements OrderMapperInterface
{
 /**
  * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
  * @param array<int, mixed> $orderItems
  *
  * @return \Generated\Shared\Transfer\OrderTransfer
  */
    public function mapOrderItemsToOrderTransfer(OrderTransfer $orderTransfer, array $orderItems): OrderTransfer
    {
        foreach ($orderItems as $orderItemEntity) {
            $itemTransfer = (new ItemTransfer())->fromArray($orderItemEntity->toArray(), true);
            $orderTransfer->addItem($itemTransfer);
        }

        return $orderTransfer;
    }
}
