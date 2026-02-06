<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms;

use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
use Generated\Shared\Transfer\OrderItemFilterTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class MolliePaymentCaptureCommandPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return void
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): void
    {
        $orderTransfer = (new OrderTransfer())->fromArray($orderEntity->toArray(), true);
        $orderItemIds = $this->getOrderItemIds($orderItems);
        $orderItemFilterTransfer = new OrderItemFilterTransfer();
        $orderItemFilterTransfer->setSalesOrderItemIds($orderItemIds);
        $itemCollectionTransfer = $this->getFactory()->getSalesFacade()->getOrderItems($orderItemFilterTransfer);

        $molliePaymentCaptureRequestTransfer = (new MolliePaymentCaptureRequestTransfer())
            ->setItems($itemCollectionTransfer)
            ->setOrder($orderTransfer);

        $this->getFacade()->capturePayment($molliePaymentCaptureRequestTransfer);
    }

    /**
     * @param array $orderItems
     *
     * @return array
     */
    protected function getOrderItemIds(array $orderItems): array
    {
        $orderItemIds = [];
        foreach ($orderItems as $orderItem) {
            $orderItemIds[] = $orderItem->getIdSalesOrderItem();
        }

        return $orderItemIds;
    }
}
