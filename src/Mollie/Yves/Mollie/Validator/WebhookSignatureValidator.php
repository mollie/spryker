<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Validator;

use Mollie\Yves\Mollie\MollieConfig;

class WebhookSignatureValidator implements WebhookSignatureValidatorInterface
{
    /**
     * @param \Mollie\Yves\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
    }

    /**
     * @param string $requestBody
     * @param string $receivedSignature
     *
     * @return bool
     */
    public function isValid(string $requestBody, string $receivedSignature): bool
    {
        $signature = str_replace('sha256=', '', $receivedSignature);
        $calculatedSignature = hash_hmac('sha256', $requestBody, $this->config->getMollieNextGenWebhookSigningSecret());

        return hash_equals($calculatedSignature, $signature);
    }
}
