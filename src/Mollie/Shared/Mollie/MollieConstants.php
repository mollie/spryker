<?php

declare(strict_types = 1);

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
    public const MOLLIE_EXPIRATION_WARNING_THRESHOLD = 'MOLLIE:EXPIRATION_WARNING_THRESHOLD';

    /**
     * @var string
     */
    public const MOLLIE_WEBHOOK_URL = 'MOLLIE:WEBHOOK_URL';

    /**
     * @var string
     */
    public const MOLLIE_NEXT_GEN_WEBHOOK_SIGNING_SECRET = 'MOLLIE:MOLLIE_NEXT_GEN_WEBHOOK_SIGNING_SECRET';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TRANSACTION_STORAGE_KEY_PREFIX = 'mollie:payment';

    /**
     * @var int
     */
    public const MOLLIE_PAYMENT_TRANSACTION_STORAGE_TTL = 300;

    /**
     * @var string
     */
    public const MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL = 'MOLLIE:TEST_ENVIRONMENT_WEBHOOK_URL';

    /**
     * @var string
     */
    public const MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED = 'MOLLIE:MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED';

    /**
     * @var string
     */
    public const MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC = 'MOLLIE:MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC';

    /**
     * @var string
     */
    public const MOLLIE_DEBUG_MODE = 'MOLLIE:MOLLIE_DEBUG_MODE';

    /**
     * @var string
     */
    public const MOLLIE_INCLUDE_WALLETS = 'MOLLIE:MOLLIE_INCLUDE_WALLETS';

    /**
     * @var string
     */
    public const MOLLIE_GET_METHODS_API_DEFAULT_AMOUNT_VALUE = 'MOLLIE_GET_METHODS_API_DEFAULT_AMOUNT_VALUE';

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
    public const MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE = 'MOLLIE:MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE';

    /**
     * @var string
     */
    public const SUCCESS_MESSAGE = 'success';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT = 'checkout';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF = 'href';

    public const string MOLLIE_LOGGER_OFF = 'Off';

    public const string MOLLIE_LOGGER_BASIC = 'Basic';

    public const string MOLLIE_LOGGER_EXTENSIVE = 'Extensive';

    public const string MOLLIE_QUERY_PARAMETER_SHOW_ONLY_ENABLED = 'showOnlyEnabledPaymentMethods';

    public const string MOLLIE_SEQUENCE_TYPE_ONE_OFF = 'oneoff';

    public const string LOGO_URL = 'LogoUrl';

    public const string IS_LOGO_VISIBLE = 'IsLogoVisible';

    public const string MOLLIE_PAYMENT_LINK_EXPIRATION_TIME = 'MOLLIE:MOLLIE_PAYMENT_LINK_EXPIRATION_TIME';

    public const string PRODUCT_TYPE_PHYSICAL = 'physical';

    public const string PRODUCT_TYPE_SHIPPING_FEE = 'shipping_fee';

    public const string MOLLIE_BNPL_PAYMENT_METHODS = 'MOLLIE:MOLLIE_BNPL_PAYMENT_METHODS';

    public const string QUERY_CURRENCY = 'currency';

    public const string QUERY_MOLLIE_PAYMENT_METHOD_ID = 'mollie_payment_method_id';

    public const string QUERY_MOLLIE_PAYMENT_METHOD_CONFIG_ID = 'id-mollie-payment-method-config';
}
