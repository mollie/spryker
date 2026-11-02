<?php

declare(strict_types = 1);

namespace Mollie\Yves\Mollie\Validator;

use Mollie\Yves\Mollie\MollieConfig;

class ApplePayValidationUrlValidator implements ApplePayValidationUrlValidatorInterface
{
    /**
     * @var string
     */
    protected const REQUIRED_SCHEME = 'https';

    /**
     * @param \Mollie\Yves\Mollie\MollieConfig $config
     */
    public function __construct(protected MollieConfig $config)
    {
    }

    /**
     * @param string $applePayValidationUrl
     *
     * @return bool
     */
    public function isValid(string $applePayValidationUrl): bool
    {
        $scheme = parse_url($applePayValidationUrl, PHP_URL_SCHEME);
        if ($scheme !== static::REQUIRED_SCHEME) {
            return false;
        }

        $host = parse_url($applePayValidationUrl, PHP_URL_HOST);
        $allowedHosts = $this->config->getApplePayValidationUrlAllowedHosts();

        return in_array($host, $allowedHosts, true);
    }
}
