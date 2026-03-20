<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition\PaymentLink;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 */
class MollieIsPaymentLinkCreationFailedConditionPlugin extends AbstractPlugin implements ConditionInterface
{
 /**
  * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
  *
  * @return bool
  */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        $idSalesOrder = $orderItem->getFkSalesOrder();

        if (!$idSalesOrder) {
            return true;
        }

        return $this->getFacade()->isPaymentLinkCreationFailed($idSalesOrder);
    }
}
