<?php

declare(strict_types=1);

namespace Mollie\Zed\Mollie\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractRepository;

class MollieRepository extends AbstractRepository implements MollieRepositoryInterface
{
    /**
     * @param string $paymentId
     *
     * @return array<int, mixed>
     */
    public function getOrderItemsByPaymentId(string $paymentId): array
    {
        return [];
    }
}
