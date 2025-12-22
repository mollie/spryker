<?php

declare(strict_types=1);

namespace Mollie\Shared\Mollie;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface MollieConstants
{
 /**
  * @var string
  */
    public const MOLLIE = 'MOLLIE';

    /**
     * @var string
     */
    public const MOLLIE_API_KEY = 'MOLLIE:MOLLIE_API_KEY';

    /**
     * @var string
     */
    public const MOLLIE_PROFILE_ID = 'MOLLIE:PROFILE_ID';

    /**
     * @var string
     */
    public const MOLLIE_TEST_MODE = 'MOLLIE:TEST_MODE';

    /**
     * @var string
     */
    public const MOLLIE_REDIRECT_URL = 'MOLLIE:MOLLIE_REDIRECT_URL';

    /**
     * @var string
     */
    public const MOLLIE_WEBHOOK_URL = 'MOLLIE:MOLLIE_WEBHOOK_URL';

    /**
     * @var string
     */
    public const MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING = 'MOLLIE:MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING';
}
