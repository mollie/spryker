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
    public const MOLLIE_PROVIDER_APPLE_PAY = 'MollieApplePayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_PRZELEWY24 = 'MolliePrzelewy24Payment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_MULTIBANCO = 'MollieMultibancoPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BANCOMAT_PAY = 'MollieBancomatPayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BIZUM = 'MollieBizumPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BLIK = 'MollieBLIKPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_PAYCONIQ = 'MolliePayconiqPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_BILLIE = 'MollieBilliePayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_RIVERTY = 'MollieRivertyPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_IDEAL_IN3 = 'MollieIdealIn3Payment';

    /**
     * @var string
     */
    public const MOLLIE_PROVIDER_ALMA = 'MollieAlmaPayment';

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
     * @var string
     */
    public const MOLLIE_PAYMENT_APPLE_PAY = 'mollieApplePayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_PRZELEWY24 = 'molliePrzelewy24Payment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_MULTIBANCO = 'mollieMultibancoPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BANCOMAT_PAY = 'mollieBancomatPayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BIZUM = 'mollieBizumPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BILLIE = 'mollieBilliePayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_IDEAL_IN3 = 'mollieIdealIn3Payment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_RIVERTY = 'mollieRivertyPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_ALMA = 'mollieAlmaPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TRUSTLY = 'mollieTrustlyPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TRUSTLY_PROVIDER = 'MollieTrustlyPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_MB_WAY = 'mollieMbWayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_MB_WAY_PROVIDER = 'MollieMbWayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_SWISH = 'mollieSwishPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_SWISH_PROVIDER = 'MollieSwishPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_SATISPAY = 'mollieSatispayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_SATISPAY_PROVIDER = 'MollieSatispayPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TWINT = 'mollieTwintPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TWINT_PROVIDER = 'MollieTwintPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_VIPPS = 'mollieVippsPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_VIPPS_PROVIDER = 'MollieVippsPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_BLIK = 'mollieBLIKPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_PAYCONIQ = 'molliePayconiqPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_TRANSACTION_STORAGE_KEY_PREFIX = 'mollie:payment';

    /**
     * @var int
     */
    public const MOLLIE_PAYMENT_TRANSACTION_STORAGE_TTL = 300;

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
     * @var string
     */
    public const MOLLIE_PLUGIN_PACKAGE = 'mollie/spryker-payment';

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
    public function getMollieLoggingMode(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_DEBUG_MODE];
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

    /**
     * @return string
     */
    public function getMolliePluginPackage(): string
    {
        return static::MOLLIE_PLUGIN_PACKAGE;
    }
}
