<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Dependency\Facade;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\OrderItemFilterTransfer;
use Spryker\Zed\Sales\Business\SalesFacadeInterface;

class MollieToSalesFacadeBridge implements MollieToSalesFacadeInterface
{
    protected SalesFacadeInterface $salesFacade;

    /**
     * @param \Spryker\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderItemFilterTransfer $orderItemFilterTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    public function getOrderItems(OrderItemFilterTransfer $orderItemFilterTransfer): ItemCollectionTransfer
    {
        return $this->salesFacade->getOrderItems($orderItemFilterTransfer);
    }
}
