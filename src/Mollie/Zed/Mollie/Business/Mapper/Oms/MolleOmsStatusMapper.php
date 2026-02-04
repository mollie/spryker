<?php

namespace Mollie\Zed\Mollie\Business\Mapper\Oms;

use Mollie\Zed\Mollie\Dependency\Events\MollieStateMachineEvents;
use Mollie\Zed\Mollie\MollieConfig;

class MolleOmsStatusMapper implements MolleOmsStatusMapperInterface
{
    /**
     * @param \Mollie\Zed\Mollie\MollieConfig $mollieConfig
     */
    public function __construct(protected MollieConfig $mollieConfig)
    {
    }

    /**
     * @var array<string, string>
     */
    protected const STATUS_TO_EVENT_MAP = [
        MollieConfig::PAID => MollieStateMachineEvents::OMS_MOLLIE_PAYMENT_PAID,
        MollieConfig::CANCELED => MollieStateMachineEvents::OMS_MOLLIE_PAYMENT_CANCELED,
        MollieConfig::EXPIRED => MollieStateMachineEvents::OMS_MOLLIE_PAYMENT_EXPIRED,
        MollieConfig::FAILED => MollieStateMachineEvents::OMS_MOLLIE_PAYMENT_FAILED,
    ];

    /**
     * @var array<string, string>
     */
    protected const REFUND_STATUS_TO_EVENT_MAP = [
        MollieConfig::PROCESSING => MollieStateMachineEvents::OMS_MOLLIE_REFUND_PROCESSING,
        MollieConfig::REFUNDED => MollieStateMachineEvents::OMS_MOLLIE_PAYMENT_REFUNDED,
        MollieConfig::FAILED => MollieStateMachineEvents::OMS_MOLLIE_REFUND_FAILED,
    ];

    /**
     * @param string $mollieStatus
     *
     * @return string|null
     */
    public function mapMolliePaymentStatusToOmsStatus(string $mollieStatus): string|null
    {
        return static::STATUS_TO_EVENT_MAP[$mollieStatus] ?? null;
    }

    /**
     * @param string $mollieStatus
     *
     * @return string|null
     */
    public function mapMollieRefundStatusToOmsStatus(string $mollieStatus): string|null
    {
        return static::REFUND_STATUS_TO_EVENT_MAP[$mollieStatus] ?? null;
    }
}
