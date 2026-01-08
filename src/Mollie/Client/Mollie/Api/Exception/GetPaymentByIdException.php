<?php

declare(strict_types=1);

namespace Mollie\Client\Mollie\Api\Exception;

use Exception;
use Throwable;

class GetPaymentByIdException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
