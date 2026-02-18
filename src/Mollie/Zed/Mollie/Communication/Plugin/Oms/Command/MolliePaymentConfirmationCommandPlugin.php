<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Communication\MollieCommunicationFactory getFactory()
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class MolliePaymentConfirmationCommandPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * @param array<int, object> $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array<int, mixed>
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $localeTransfer = (new LocaleTransfer())
            ->setIdLocale($orderEntity->getFkLocale());

        $totalsTransfer = (new TotalsTransfer())
            ->fromArray($orderEntity->getLastOrderTotals()->toArray(), true);

        $orderTransfer = (new OrderTransfer())
            ->setFirstName($orderEntity->getFirstName())
            ->setLastName($orderEntity->getLastName())
            ->setOrderReference($orderEntity->getOrderReference())
            ->setEmail($orderEntity->getEmail())
            ->setIdSalesOrder($orderEntity->getIdSalesOrder())
            ->setLocale($localeTransfer)
            ->setTotals($totalsTransfer)
            ->setCreatedAt($orderEntity->getCreatedAt())
            ->setCurrencyIsoCode($orderEntity->getCurrencyIsoCode());

        $this->getFacade()->sendPaymentConfirmationMail($orderTransfer);

        return [];
    }
}
