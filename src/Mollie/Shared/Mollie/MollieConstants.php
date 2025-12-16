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
}
