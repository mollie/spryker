<?php

declare(strict_types = 1);

namespace Mollie\Client\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Client\Kernel\AbstractBundleConfig;

/**
 * @method \Mollie\Shared\Mollie\MollieConfig getSharedConfig()
 */
class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_CARD_TOKEN = 'cardToken';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_PAYPAL_SESSION_ID = 'sessionId';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_PAYPAL_DIGITAL_GOODS = 'digitalGoods';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_ORDER_REFERENCE = 'orderReference';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS = '_links';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_EMBEDDED = '_embedded';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_PAYMENT_LINKS = 'payment_links';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_BANK_TRANSFER_DUE_DATE = 'dueDate';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_BANK_TRANSFER_BILLING_EMAIL = 'billingAddress.email';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_KLARNA_EXTRA_MERCHANT_DATA = 'extraMerchantData';

    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_APPLE_PAY_PAYMENT_TOKEN = 'applePayPaymentToken';

    /**
     * @var int
     */
    public const MOLLIE_PAYMENT_METHODS_STORAGE_KEY_TTL = 21600;

    /**
     * @var string
     */
    protected const CACHE_KEY_PREFIX_FOR_ALL_PAYMENT_METHODS = 'all_payment_methods';

    /**
     * @var string
     */
    protected const CACHE_KEY_PREFIX_FOR_ENABLED_PAYMENT_METHODS = 'enabled_payment_methods';

    /**
     * @var string
     */
    protected const MOLLIE_AUTOMATIC_CAPTURE_MODE = 'automatic';

    /**
     * @var string
     */
    protected const MOLLIE_MANUAL_CAPTURE_MODE = 'manual';

    /**
     * @var string
     */
    public const RESPONSE_CREATE_PAYMENT_LINK_STATUS_OPEN = 'open';

    /**
     * @var string
     */
    public const SPRYKER_CORE_PACKAGE = 'spryker-feature/spryker-core';

    /**
     * @var string
     */
    public const MOLLIE_PLUGIN_PACKAGE = 'mollie/spryker-payment';

    /**
     * @var string
     */
    public const UAP_IDENTIFIER = 'uap/84vsKAknyrfvkQHs';

    /**
     * @return string
     */
    public function getMollieRedirectUrl(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_REDIRECT_URL];
    }

    /**
     * @return string
     */
    public function getMollieWebhookUrl(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_WEBHOOK_URL];
    }

    /**
     * @return string
     */
    public function getTestEnvironmentMollieWebhookUrl(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL];
    }

    /**
     * @return array<string, string>
     */
    public function getMollieOmsToPaymentMethodMapping(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING];
    }

    /**
     * @return array<string, string>
     */
    public function getMolliePaymentMethodsManualCapture(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE];
    }

    /**
     * @param string $paymentMethodKey
     *
     * @return string|null
     */
    public function getMolliePaymentMethod(string $paymentMethodKey): ?string
    {
        $mapping = $this->getMollieOmsToPaymentMethodMapping();

        return $mapping[$paymentMethodKey] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMollieApiKey(): string|null
    {
        return $this->getSharedConfig()->getMollieApiKey();
    }

    /**
     * @return string|null
     */
    public function getMollieProfileId(): string|null
    {
        return $this->getSharedConfig()->getMollieProfileid();
    }

    /**
     * @return bool
     */
    public function isMollieTestModeEnabled(): bool
    {
        return $this->getSharedConfig()->isMollieTestModeEnabled();
    }

    /**
     * @return string
     */
    public function getMollieLoggingMode(): string
    {
        return $this->getSharedConfig()->getMollieLoggingMode();
    }

    /**
     * @return int
     */
    public function getMolliePaymentMethodsStorageKeyTTL(): int
    {
        return static::MOLLIE_PAYMENT_METHODS_STORAGE_KEY_TTL;
    }

      /**
       * @return string
       */
    public function getCacheKeyPrefixForAllPaymentMethods(): string
    {
        return static::CACHE_KEY_PREFIX_FOR_ALL_PAYMENT_METHODS;
    }

    /**
     * @return string
     */
    public function getCacheKeyPrefixForEnabledPaymentMethods(): string
    {
        return static::CACHE_KEY_PREFIX_FOR_ENABLED_PAYMENT_METHODS;
    }

    /**
     * @return string
     */
    public function getMollieAutomaticCaptureMode(): string
    {
        return static::MOLLIE_AUTOMATIC_CAPTURE_MODE;
    }

    /**
     * @return string
     */
    public function getMollieManualCaptureMode(): string
    {
        return static::MOLLIE_MANUAL_CAPTURE_MODE;
    }

    /**
     * @return string
     */
    public function getUapIdentifier(): string
    {
        return static::UAP_IDENTIFIER;
    }

    /**
     * @return string
     */
    public function getSprykerCorePackage(): string
    {
        return static::SPRYKER_CORE_PACKAGE;
    }

    /**
     * @return string
     */
    public function getMolliePluginPackage(): string
    {
        return static::MOLLIE_PLUGIN_PACKAGE;
    }
}
