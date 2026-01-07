<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Client\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const REQUEST_PARAMETER_CREATE_PAYMENT_CARD_TOKEN = 'cardToken';

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
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_EMBEDDED = '_embedded';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_CHECKOUT = 'checkout';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_LINKS_HREF = 'href';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_METADATA = 'metadata';

    /**
     * @var string
     */
    public const RESPONSE_PARAMETER_CREATE_PAYMENT_ID = 'id';

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
    public function getMollieApiKey(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_API_KEY];
    }

    /**
     * @return array<string, string>
     */
    public function getMollieOmsToPaymentMethodMapping(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING];
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
}
