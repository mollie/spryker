<?php

namespace Mollie\Zed\Mollie\Business\Filter;

class MollieRefundFilter implements MollieRefundFilterInterface
{
    /**
     * @var string
     */
    protected const REFUND_STATUS_REFUNDED = 'refunded';

    /**
     * @var string
     */
    protected const REFUND_STATUS_PROCESSING = 'processing';

    /**
     * @var string
     */
    protected const REFUND_STATUS_FAILED = 'failed';

    /**
     * @var array<string>
     */
    protected const TERMINAL_STATUSES = [
        self::REFUND_STATUS_REFUNDED,
        self::REFUND_STATUS_PROCESSING,
        self::REFUND_STATUS_FAILED,
    ];

    /**
     * @param array<int, mixed> $mollieRefunds
     *
     * @return array<int, mixed>
     */
    public function filterRefundsByStatus(array $mollieRefunds): array
    {
        $filteredMollieRefunds = [];

        foreach ($mollieRefunds as $mollieRefund) {
            if (in_array($mollieRefund['status'], static::TERMINAL_STATUSES, true)) {
                $filteredMollieRefunds[] = $mollieRefund;
            }
        }

        return $filteredMollieRefunds;
    }
}
