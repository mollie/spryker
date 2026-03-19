<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Validator;

interface WebhookSignatureValidatorInterface
{
    /**
     * @param string $requestBody
     * @param string $receivedSignature
     *
     * @return bool
     */
    public function isValid(string $requestBody, string $receivedSignature): bool;
}
