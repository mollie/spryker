<?php

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MollieRefundTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MollieRefundWriter implements MollieRefundWriterInterface
{
    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(
        protected MollieEntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\MollieRefundTransfer $mollieRefundTransfer
     *
     * @return void
     */
    public function addMollieRefundData(int $idSalesOrder, MollieRefundTransfer $mollieRefundTransfer): void
    {
        $this->entityManager->addMollieRefundData($idSalesOrder, $mollieRefundTransfer);
    }
}
