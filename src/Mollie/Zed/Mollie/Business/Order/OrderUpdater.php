<?php

namespace Mollie\Zed\Mollie\Business\Order;

use Generated\Shared\Transfer\OrderCollectionRequestTransfer;
use Generated\Shared\Transfer\OrderCollectionResponseTransfer;
use Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;
use Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface;
use Psr\Log\LoggerInterface;

class OrderUpdater implements OrderUpdaterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieRepositoryInterface $repository
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     * @param \Mollie\Zed\Mollie\Dependency\Facade\MollieToOmsInterface $omsFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected MollieRepositoryInterface $repository,
        protected MollieEntityManagerInterface $entityManager,
        protected MollieToOmsInterface $omsFacade,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OrderCollectionResponseTransfer
     */
    public function updateOrderCollection(OrderCollectionRequestTransfer $updateOrderCollectionRequestTransfer): OrderCollectionResponseTransfer
    {
        $orderItems = $this->repository->getOrderItemsByPaymentId($updateOrderCollectionRequestTransfer->getId());

        return new OrderCollectionResponseTransfer();
    }
}
