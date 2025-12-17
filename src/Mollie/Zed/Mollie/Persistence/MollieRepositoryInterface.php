<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

interface MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return array<int, mixed>
     */
    public function getOrderItemsByPaymentId(string $paymentId): array;
}
