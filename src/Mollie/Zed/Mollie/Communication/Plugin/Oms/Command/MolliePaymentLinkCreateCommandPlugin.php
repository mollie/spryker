<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\MollieConfig getConfig()
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
//        $molliePaymentLinkTransfer = new MolliePaymentLinkTransfer();
//
//        $orderTotals = $orderEntity->getOrderTotals()->getData();
//
//        $molliePaymentLinkTransfer
//            ->setDescription($orderEntity->getOrderReference())
//            ->setAmount(
//                (new MollieAmountTransfer())
//                    ->setValue(20.00)
//                    ->setCurrency('EUR'),
//            )
//            ->setRedirectUrl($this->getConfig()->getMollieRedirectUrl())
//            ->setWebhookUrl($this->getConfig()->getTestEnvironmentMollieWebhookUrl())
//            ->setExpiresAt('2026-06-01T00:00:00')
//            ->setReusable(true);
//
//            $this->getFacade()->createPaymentLink($molliePaymentLinkTransfer);

        return [];
    }
}
