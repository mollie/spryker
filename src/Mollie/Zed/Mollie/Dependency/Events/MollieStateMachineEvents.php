<?php

namespace Mollie\Zed\Mollie\Dependency\Events;

interface MollieStateMachineEvents
{
    /**
     * @var string
     */
    public const OMS_MOLLIE_START_PAYMENT = 'start payment';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_PAID = 'payment paid';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_CANCELED = 'payment canceled';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_EXPIRED = 'payment expired';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_FAILED = 'payment failed';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_REFUNDED = 'payment refunded';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_READY_FOR_SHIPMENT = 'ready for shipment';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_SHIP_ORDER = 'ship order';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_CONFIRM_DELIVERY = 'confirm delivery';

    /**
     * @var string
     */
    public const OMS_MOLLIE_PAYMENT_CLOSE = 'close';
}
