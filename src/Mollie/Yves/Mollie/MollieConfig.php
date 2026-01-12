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
    protected const ERROR_MESSAGE_ORDER_STATUS_PAYMENT_ERROR = 'An error occurred while processing your payment (status: %s). Please try again.';

    /**
     * @return string
     */
    public function getProfileId(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_PROFILE_ID];
    }

    /**
     * @return string
     *
     * @var string
     */
    public const ROUTE_PAYMENT_REDIRECT = 'payment/status';

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_TEST_MODE];
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
    public function getPaymentFailedMessage(): string
    {
        return static::ERROR_MESSAGE_ORDER_STATUS_PAYMENT_ERROR;
    }
}
