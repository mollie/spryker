<?php

declare(strict_types=1);

namespace Mollie\Shared\Mollie;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface MollieConstants
{
 /**
  * @var string
  */
    public const MOLLIE = 'MOLLIE';

    /**
     * @var string
     */
    public const MOLLIE_API_KEY = 'MOLLIE:MOLLIE_API_KEY';

    /**
     * @var string
     */
    public const MOLLIE_PROFILE_ID = 'MOLLIE:PROFILE_ID';

    /**
     * @var string
     */
    public const MOLLIE_TEST_MODE = 'MOLLIE:TEST_MODE';

    /**
     * @var string
     */
    public const MOLLIE_WEBHOOK_URL = 'MOLLIE:WEBHOOK_URL';

    /**
     * @var string
     */
    public const MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL = 'MOLLIE:TEST_ENVIRONMENT_WEBHOOK_URL';

    // Payment statuses from Mollie
    /**
     * @var string
     */
    public const STATUS_OPEN = 'open';

    /**
     * @var string
     */
    public const STATUS_CANCELED = 'canceled';

    /**
     * @var string
     */
    public const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const STATUS_AUTHORIZED = 'authorized';

    /**
     * @var string
     */
    public const STATUS_EXPIRED = 'expired';

    /**
     * @var string
     */
    public const STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    public const STATUS_PAID = 'paid';

    // OMS Events
    /**
     * @var string
     */
    public const OMS_EVENT_PAYMENT_PAID = 'payment_paid';

    /**
     * @var string
     */
    public const OMS_EVENT_PAYMENT_FAILED = 'payment_failed';

    /**
     * @var string
     */
    public const OMS_EVENT_PAYMENT_CANCELED = 'payment_canceled';

    /**
     * @var string
     */
    public const OMS_EVENT_PAYMENT_AUTHORIZED = 'payment_authorized';

    /**
     * @var string
     */
    public const MOLLIE_REDIRECT_URL = 'MOLLIE:MOLLIE_REDIRECT_URL';

    /**
     * @var string
     */
    public const MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING = 'MOLLIE:MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING';

    /**
     * @var string
     */
    public const SUCCESS_MESSAGE = 'success';

    public const string MOLLIE_AVAILABLE_METHODS_STORAGE_KEY = 'mollie:availableMethods';

    public const string MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED = 'showOnlyEnabled';
}
