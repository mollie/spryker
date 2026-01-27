<?php


declare(strict_types = 1);

namespace Mollie\Shared\Mollie;

use Spryker\Shared\Kernel\AbstractSharedConfig;

class MollieConfig extends AbstractSharedConfig
{
    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_CREDIT_CARD = 'MollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_PAYPAL = 'MolliePayPalPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BANK_TRANSFER = 'MollieBankTransferPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_KLARNA = 'MollieKlarnaPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_KLARNA_PAY_LATER = 'MollieKlarnaPayLaterPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_KLARNA_PAY_NOW = 'MollieKlarnaPayNowPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_KLARNA_SLICE_IT = 'MollieKlarnaSliceItPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_EPS = 'MollieEpsPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_IDEAL = 'MollieIdealPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BANCONTACT = 'MollieBancontactPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_KBC = 'MollieKbcPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_PAY_BY_BANK = 'MolliePayByBankPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_CREDIT_CARD = 'mollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BANK_TRANSFER = 'mollieBankTransferPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_KLARNA = 'mollieKlarnaPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_KLARNA_PAY_LATER = 'mollieKlarnaPayLaterPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_KLARNA_PAY_NOW = 'mollieKlarnaPayNowPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_KLARNA_SLICE_IT = 'mollieKlarnaSliceItPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_PAYPAL = 'molliePayPalPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_EPS = 'mollieEpsPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_IDEAL = 'mollieIdealPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BANCONTACT = 'mollieBancontactPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_KBC = 'mollieKbcPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_PAY_BY_BANK = 'molliePayByBankPayment';

    /**
     * @var array<string>
     */
    public const MOLLIE_PAYMENT_STATUS_FAILED = ['failed', 'expired', 'canceled'];

     /**
      * @var string
      */
    protected const CACHE_KEY_IDENTIFIER_FOR_ALL_PAYMENT_METHODS = 'all_payment_methods';

     /**
      * @var string
      */
    protected const CACHE_KEY_IDENTIFIER_FOR_ENABLED_PAYMENT_METHODS = 'enabled_payment_methods';

    /**
     * @return string|null
     */
    public function getMollieApiKey(): string|null
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_API_KEY];
    }

    /**
     * @return string|null
     */
    public function getMollieProfileId(): string|null
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PROFILE_ID];
    }

    /**
     * @return bool
     */
    public function isMollieTestModeEnabled(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }

    /**
     * @return string
     */
    public function getCacheKeyIdentifierForAllPaymentMethods(): string
    {
        return static::CACHE_KEY_IDENTIFIER_FOR_ALL_PAYMENT_METHODS;
    }

    /**
     * @return string
     */
    public function getCacheKeyIdentifierForEnabledPaymentMethods(): string
    {
        return static::CACHE_KEY_IDENTIFIER_FOR_ENABLED_PAYMENT_METHODS;
    }
}
