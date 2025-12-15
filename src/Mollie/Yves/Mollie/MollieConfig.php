<?php

declare(strict_types=1);

namespace Mollie\Yves\Mollie;

use Spryker\Yves\Kernel\AbstractBundleConfig;

class MollieConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'MollieCreditCardPayment';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_INVOICE = 'molliePayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_CREDIT_CARD = 'mollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_PROFILE_ID = 'profileId';

    /**
     * @var string
     */
    public const MOLLIE_TEST_MODE = 'mollieTestMode';

    /**
     * @return string
     */
    public function getMollieProfileId(): string
    {
        return $this->get(static::MOLLIE_PROFILE_ID);
    }

    /**
     * @return bool
     */
    public function isMollieTestMode(): bool
    {
        return $this->get(static::MOLLIE_TEST_MODE);
    }
}
