<?php


namespace Mollie\Zed\Mollie\Communication\Plugin\Oms;

use Exception;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class PaymentConfirmationCommandPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * @param array<int, object> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<int, mixed>
     * @throws \Exception
     *
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $orderTransfer = (new OrderTransfer())
            ->setIdSalesOrder($orderEntity->getIdSalesOrder())
            ->setCurrencyIsoCode($orderEntity->getCurrencyIsoCode());

        $this->getFactory()->getMailFacade()->handleMail($orderTransfer);

        return [];
    }
}
