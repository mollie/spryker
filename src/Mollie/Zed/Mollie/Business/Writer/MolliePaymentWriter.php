<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Business\Writer;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class MolliePaymentWriter implements MolliePaymentWriterInterface
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
     * @param \Generated\Shared\Transfer\MolliePaymentTransfer $molliePaymentTransfer
     *
     * @return void
     */
    public function addMolliePaymentData(int $idSalesOrder, MolliePaymentTransfer $molliePaymentTransfer): void
    {
        $this->entityManager->addMolliePaymentData($idSalesOrder, $molliePaymentTransfer);
    }
}
