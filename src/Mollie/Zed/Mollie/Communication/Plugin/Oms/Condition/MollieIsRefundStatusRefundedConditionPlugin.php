<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 */
class MollieIsRefundStatusRefundedConditionPlugin extends AbstractPlugin implements ConditionInterface
{
    /**
     * @var string
     */
    protected const REFUND_STATUS_REFUNDED = 'refunded';

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        $refund = $this->getRepository()->findRefundByOrderItem($orderItem->getIdSalesOrderItem());

        return $refund->getRefund()->getStatus() === static::REFUND_STATUS_REFUNDED;
    }
}
