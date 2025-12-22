<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Propel\Runtime\Collection\ObjectCollection;

interface MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    public function getOrderItemsByPaymentId(string $paymentId): ObjectCollection;
}
