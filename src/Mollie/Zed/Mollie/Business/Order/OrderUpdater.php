<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Order;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class OrderUpdater implements OrderUpdaterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        $orderCollectionResponseTransfer = new OrderCollectionResponseTransfer();

        $this->entityManager->updateMolliePaymentWithStatus($updateOrderCollectionRequestTransfer);

        $orderCollectionResponseTransfer->setIsSuccess(true);

        return $orderCollectionResponseTransfer;
    }
}
