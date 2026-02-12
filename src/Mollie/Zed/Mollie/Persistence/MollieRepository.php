<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Generated\Shared\Transfer\MollieRefundResponseTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Mollie\Zed\Mollie\Persistence\MolliePersistenceFactory getFactory()
 */
class MollieRepository extends AbstractRepository implements MollieRepositoryInterface
{
    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPaymentByOrderId(string $orderId): MolliePaymentTransfer
    {
        $spyPaymentMollieRecord = $this->getFactory()
            ->createSpyPaymentMollieQuery()
            ->filterByFkSalesOrder($orderId)
            ->findOne();

        if (!$spyPaymentMollieRecord) {
            return new MolliePaymentTransfer();
        }

        return $this->getFactory()
            ->createMollieOrderMapper()
            ->mapFromSpyPaymentMollieEntityToMolliePaymentTransfer($spyPaymentMollieRecord);
    }

    /**
     * @param int $orderItemId
     *
     * @return \Generated\Shared\Transfer\MollieRefundResponseTransfer
     */
    public function findRefundByOrderItem(int $orderItemId): MollieRefundResponseTransfer
    {
        $spyRefundMollieRecord = $this->getFactory()
            ->createSpyRefundMollieQuery()
            ->filterByFkSalesOrderItem($orderItemId)
            ->findOne();

        return $this->getFactory()
            ->createMollieRefundMapper()
            ->mapFromSpyRefundMollieEntityToMollieRefundTransfer($spyRefundMollieRecord);
    }
}
