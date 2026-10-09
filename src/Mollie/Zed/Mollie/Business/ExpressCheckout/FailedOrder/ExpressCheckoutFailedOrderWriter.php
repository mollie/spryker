<?php

declare(strict_types = 1);

namespace Mollie\Zed\Mollie\Business\ExpressCheckout\FailedOrder;

use Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer;
use Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface;

class ExpressCheckoutFailedOrderWriter implements ExpressCheckoutFailedOrderWriterInterface
{
    /**
     * Status until the payment webhook updates the record with the Mollie payment status.
     *
     * @var string
     */
    protected const STATUS_ORDER_FAILED = 'order_failed';

    /**
     * @param \Mollie\Zed\Mollie\Persistence\MollieEntityManagerInterface $entityManager
     */
    public function __construct(protected MollieEntityManagerInterface $entityManager)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MollieExpressCheckoutFailedOrderTransfer
     */
    public function create(
        MollieExpressCheckoutFailedOrderTransfer $mollieExpressCheckoutFailedOrderTransfer,
    ): MollieExpressCheckoutFailedOrderTransfer {
        $mollieExpressCheckoutFailedOrderTransfer->setStatus(static::STATUS_ORDER_FAILED);

        return $this->entityManager->createExpressCheckoutFailedOrder($mollieExpressCheckoutFailedOrderTransfer);
    }
}
