<?php

namespace Mollie\Zed\Mollie\Communication\Plugin\Oms\Condition;

use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * @method \Mollie\Zed\Mollie\Business\MollieFacadeInterface getFacade()
 * @method \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface getRepository()
 */
class MollieIsPaymentStatusFailedConditionPlugin extends AbstractPlugin implements ConditionInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        $payment = $this->getRepository()->getPaymentByOrderId($orderItem->getFkSalesOrder());

        return $payment->getStatus() === static::PAYMENT_STATUS_FAILED;
    }
}
