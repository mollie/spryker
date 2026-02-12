<?php

namespace Mollie\Zed\Mollie\Business\Filter;

interface MollieRefundFilterInterface
{
    /**
     * @param array<int, mixed> $mollieRefunds
     *
     * @return array<int, mixed>
     */
    public function filterRefundsByStatus(array $mollieRefunds): array;
}
