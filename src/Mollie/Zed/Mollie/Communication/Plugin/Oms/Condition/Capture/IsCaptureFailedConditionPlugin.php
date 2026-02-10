<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\AbstractCondition;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

class IsCaptureFailedConditionPlugin extends AbstractCondition implements ConditionInterface
{
    public function check(SpySalesOrderItem $orderItem)
    {
        return false;
    }
}
