<?php


declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const ORDER_REFERENCE_QUERY_PARAM_NAME = 'orderReference';

    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_UUID_SESSION_KEY = 'mollie_express_checkout_uuid';

    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_SESSION_ID_SESSION_KEY = 'mollie_express_checkout_session_id';

    /**
     * @var string
     */
    protected const EXPRESS_CHECKOUT_QUOTE_STORAGE_KEY_PREFIX = 'mollie:express-checkout:quote:';

    /**
     * Cart copy for Mollie's shipping callback; only needed while the shopper is in the express sheet.
     *
     * @var int
     */
    protected const EXPRESS_CHECKOUT_QUOTE_STORAGE_TTL = 3600;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ORDER_STATUS_PAYMENT_ERROR = 'Payment did not get processed (status: %s). Please try again.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PAYMENT_ID_DOESNT_EXIST = 'Payment ID does not exist.';

    /**
     * @return string
     */
    public function getMollieCreditCardComponentsJsSrc(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC];
    }

    /**
     * @return string
     */
    public function getMollieExpressCheckoutJsSrc(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_EXPRESS_CHECKOUT_JS_SRC];
    }

    /**
     * @return string
     */
    public function getProfileId(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PROFILE_ID];
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }

    /**
     * @return bool
     */
    public function isMollieCreditCardComponentEnabled(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED];
    }

    /**
     * @return array<string, string>
     */
    public function getMollieOmsToPaymentMethodMapping(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING];
    }

    /**
     * @return string
     */
    public function getMollieNextGenWebhookSigningSecret(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_NEXT_GEN_WEBHOOK_SIGNING_SECRET];
    }

    /**
     * @return string
     */
    public function getOrderReferenceQueryParamName(): string
    {
        return static::ORDER_REFERENCE_QUERY_PARAM_NAME;
    }

    /**
     * @return string
     */
    public function getExpressCheckoutUuidSessionKey(): string
    {
        return static::EXPRESS_CHECKOUT_UUID_SESSION_KEY;
    }

    /**
     * @return string
     */
    public function getExpressCheckoutSessionIdSessionKey(): string
    {
        return static::EXPRESS_CHECKOUT_SESSION_ID_SESSION_KEY;
    }

    /**
     * @param string $mollieSessionId
     *
     * @return string
     */
    public function getExpressCheckoutQuoteStorageKey(string $mollieSessionId): string
    {
        return static::EXPRESS_CHECKOUT_QUOTE_STORAGE_KEY_PREFIX . $mollieSessionId;
    }

    /**
     * @return int
     */
    public function getExpressCheckoutQuoteStorageTtl(): int
    {
        return static::EXPRESS_CHECKOUT_QUOTE_STORAGE_TTL;
    }

    /**
     * @return string
     */
    public function getPaymentFailedMessage(): string
    {
        return static::ERROR_MESSAGE_ORDER_STATUS_PAYMENT_ERROR;
    }

    /**
     * @return string
     */
    public function getPaymentIdDoesntExistMessage(): string
    {
        return static::ERROR_MESSAGE_PAYMENT_ID_DOESNT_EXIST;
    }

    /**
     * @return array<string>
     */
    public function getMollieIncludeWallets(): array
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_INCLUDE_WALLETS] ?? [];
    }
}
