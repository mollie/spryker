<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Generated\Shared\Transfer\MolliePaymentTransfer;
use Propel\Runtime\Collection\ObjectCollection;

interface MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|null
     */
    public function getOrderItemsByPaymentId(string $paymentId): ObjectCollection|null;

    /**
     * @param string $orderId
     *
     * @return \Generated\Shared\Transfer\MolliePaymentTransfer
     */
    public function getPaymentByOrderId(string $orderId): MolliePaymentTransfer;
}
