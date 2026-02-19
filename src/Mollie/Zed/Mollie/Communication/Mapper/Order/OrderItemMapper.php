<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Mapper\Order;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class OrderItemMapper implements OrderItemMapperInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Communication\Mapper\Order\OrderTransfer $orderTransfer
     * @param array<int> $orderItemIds
     *
     * @return \Mollie\Zed\Mollie\Communication\Mapper\Order\ItemCollectionTransfer
     */
    public function mapOrderTransferToItemCollectionTransfer(
        OrderTransfer $orderTransfer,
        array $orderItemIds,
    ): ItemCollectionTransfer {
        $itemCollectionTransfer = new ItemCollectionTransfer();

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (!in_array($itemTransfer->getIdSalesOrderItem(), $orderItemIds, true)) {
                continue;
            }

            $itemCollectionTransfer->addItem($itemTransfer);
        }

        return $itemCollectionTransfer;
    }
}
