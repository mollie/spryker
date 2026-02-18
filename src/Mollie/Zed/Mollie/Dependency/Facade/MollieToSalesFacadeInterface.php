<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderItemFilterTransfer;

interface MollieToSalesFacadeInterface
{
/**
 * @param \Generated\Shared\Transfer\OrderItemFilterTransfer $orderItemFilterTransfer
 *
 * @return \Generated\Shared\Transfer\ItemCollectionTransfer
 */
    public function getOrderItems(OrderItemFilterTransfer $orderItemFilterTransfer): ItemCollectionTransfer;
}
