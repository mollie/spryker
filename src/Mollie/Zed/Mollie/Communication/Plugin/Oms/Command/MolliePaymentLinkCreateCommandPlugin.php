<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 */
class MolliePaymentLinkCreateCommandPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * @param array<mixed> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<mixed>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->findOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        $molliePaymentLinkTransfer = $this->getFacade()->processPaymentLinkData($orderTransfer);
        $this->getFacade()->createPaymentLink($molliePaymentLinkTransfer);

        return [];
    }
}
