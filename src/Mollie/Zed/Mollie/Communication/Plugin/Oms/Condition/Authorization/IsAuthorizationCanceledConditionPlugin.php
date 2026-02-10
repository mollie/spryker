<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\AbstractCondition;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

class IsAuthorizationCanceledConditionPlugin extends AbstractCondition implements ConditionInterface
{
    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return false
     */
    public function check(SpySalesOrderItem $orderItem): false
    {
        return false;
    }
}
