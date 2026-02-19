<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\MolliePaymentCaptureRequestTransfer;
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
     * @param array<string, mixed> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<string, mixed>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->findOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        $orderItemIds = $this->getOrderItemIds($orderItems);
        $itemCollectionTransfer = $this->getFactory()
            ->createOrderItemMapper()
            ->mapOrderTransferToItemCollectionTransfer($orderTransfer, $orderItemIds);

        $molliePaymentCaptureRequestTransfer = (new MolliePaymentCaptureRequestTransfer())
            ->setItems($itemCollectionTransfer)
            ->setOrder($orderTransfer);

        $this->getFacade()->capturePayment($molliePaymentCaptureRequestTransfer);

        return [];
    }

    /**
     * @param array<string, mixed> $orderItems
     *
     * @return array<int>
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
