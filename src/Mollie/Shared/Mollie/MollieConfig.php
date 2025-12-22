<?php

namespace Mollie\Shared\Mollie;

use Spryker\Shared\Kernel\AbstractSharedConfig;

class MollieConfig extends AbstractSharedConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'MollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_PAYMENT_CREDIT_CARD = 'mollieCreditCardPayment';

    /**
     * @var string
     */
    public const MOLLIE_STORAGE_KEY_PREFIX = 'mollie:payment';

    /**
     * @var int
     */
    public const MOLLIE_STORAGE_TTL = 300;

    /**
     * @var array<string>
     */
    public const MOLLIE_PAYMENT_STATUS_FAILED = ['failed', 'expired'];

    /**
     * @return string|null
     */
    public function getMollieApiKey(): string|null
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_API_KEY];
    }

    /**
     * @return string
     */
    public function getMollieTestModeEnabled(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
    }
}
