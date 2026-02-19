<?php

declare(strict_types = 1);

namespace Mollie\Shared\Mollie;

interface MolliePaymentStatusConstants
{
    /**
     * @var string
     */
    public const CAPTURED = 'succeeded';

    /**
     * @var string
     */
    public const CAPTURE_PENDING = 'pending';

    /**
     * @var string
     */
    public const CAPTURE_FAILED = 'failed';

    /**
     * @var string
     */
    public const AUTHORIZED = 'authorized';

    /**
     * @var string
     */
    public const AUTHORIZATION_CANCELED = 'canceled';

    /**
     * @var string
     */
    public const AUTHORIZATION_EXPIRED = 'expired';

    /**
     * @var string
     */
    public const AUTHORIZATION_FAILED = 'failed';
}
