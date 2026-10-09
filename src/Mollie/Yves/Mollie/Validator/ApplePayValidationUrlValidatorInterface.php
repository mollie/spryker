<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Validator;

interface ApplePayValidationUrlValidatorInterface
{
    /**
     * @param string $applePayValidationUrl
     *
     * @return bool
     */
    public function isValid(string $applePayValidationUrl): bool;
}
