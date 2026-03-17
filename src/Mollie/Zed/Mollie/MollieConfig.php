<?php


declare(strict_types = 1);

namespace Mollie\Zed\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PAID = 'paid';

    /**
     * @var string
     */
    public const AUTHORIZED = 'authorized';

    /**
     * @var string
     */
    public const EXPIRED = 'expired';

    /**
     * @var string
     */
    public const FAILED = 'failed';

    /**
     * @var string
     */
    public const CANCELED = 'canceled';

    /**
     * @var string
     */
    public const PROCESSING = 'processing';

    /**
     * @var string
     */
    public const REFUNDED = 'refunded';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_METHOD_STATUS_ACTIVATED = 'activated';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_PROVIDER = 'mollie';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_METHOD_AMOUNT_VALUE = 'value';

    /**
     * @var string
     */
    public const MOLLIE_WALLET_APPLE_PAY = 'applepay';

    /**
     * @var string
     */
    public const PAYMENT_PROVIDER_PREFIX = 'Mollie';

    /**
     * @return array<string, string>
     */
    public function getMollieOmsToPaymentMethodMapping(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING] ?? [];
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
     * @return array<string>
     */
    public function getMollieIncludeWallets(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_INCLUDE_WALLETS] ?? [];
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }

    /**
     * @return array<string>
     */
    public function getPaymentCaptureStates(): array
    {
        return ['captured', 'capture pending'];
    }
}
