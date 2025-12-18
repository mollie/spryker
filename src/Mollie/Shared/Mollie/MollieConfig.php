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
