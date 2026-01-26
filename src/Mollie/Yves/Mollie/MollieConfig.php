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
    protected const ERROR_MESSAGE_ORDER_STATUS_PAYMENT_ERROR = 'Payment did not get processed (status: %s). Please try again.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PAYMENT_ID_DOESNT_EXIST = 'Payment ID does not exist.';

    /**
     * @return string
     */
    public function getMollieCreditCardComponentJsSrc(): string
    {
        return $this->get(MollieConstants::MOLLIE)[MollieConstants::MOLLIE_CREDIT_CARD_COMPONENT_JS_SRC];
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

    /**
     * @return string
     */
    public function getPaymentIdDoesntExistMessage(): string
    {
        return static::ERROR_MESSAGE_PAYMENT_ID_DOESNT_EXIST;
    }
}
