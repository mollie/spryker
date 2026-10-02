<?php

declare(strict_types = 1);

namespace MollieTest\Yves\Mollie\Validator;

use Codeception\Test\Unit;
use Mollie\Yves\Mollie\MollieConfig;
use Mollie\Yves\Mollie\MollieFactory;
use Mollie\Yves\Mollie\Validator\ApplePayValidationUrlValidatorInterface;

class ApplePayValidationUrlValidatorTest extends Unit
{
    /**
     * @var string
     */
    protected const APPLE_PAY_VALIDATION_URL_PATH = '/paymentservices/startSession';

    /**
     * @var string
     */
    protected const ATTACKER_HOST = 'attacker.example.com';

    /**
     * @return void
     */
    public function testIsValidReturnsTrueForHttpsUrlOnEachConfiguredApplePayGatewayHost(): void
    {
        $mollieConfig = new MollieConfig();
        $applePayValidationUrlValidator = $this->createApplePayValidationUrlValidator();
        $allowedHosts = $mollieConfig->getApplePayValidationUrlAllowedHosts();

        $this->assertNotEmpty($allowedHosts);
        foreach ($allowedHosts as $allowedHost) {
            $applePayValidationUrl = 'https://' . $allowedHost . static::APPLE_PAY_VALIDATION_URL_PATH;

            $isValid = $applePayValidationUrlValidator->isValid($applePayValidationUrl);

            $this->assertTrue($isValid, $applePayValidationUrl);
        }
    }

    /**
     * @return void
     */
    public function testIsValidReturnsFalseForConfiguredApplePayGatewayHostUsedInDisguisedUrl(): void
    {
        $mollieConfig = new MollieConfig();
        $applePayValidationUrlValidator = $this->createApplePayValidationUrlValidator();

        foreach ($mollieConfig->getApplePayValidationUrlAllowedHosts() as $allowedHost) {
            $disguisedApplePayValidationUrls = [
                'http://' . $allowedHost . static::APPLE_PAY_VALIDATION_URL_PATH,
                'https://' . $allowedHost . '.' . static::ATTACKER_HOST . static::APPLE_PAY_VALIDATION_URL_PATH,
                'https://' . $allowedHost . '@' . static::ATTACKER_HOST . static::APPLE_PAY_VALIDATION_URL_PATH,
                $allowedHost,
            ];

            foreach ($disguisedApplePayValidationUrls as $disguisedApplePayValidationUrl) {
                $isValid = $applePayValidationUrlValidator->isValid($disguisedApplePayValidationUrl);

                $this->assertFalse($isValid, $disguisedApplePayValidationUrl);
            }
        }
    }

    /**
     * @dataProvider unknownApplePayValidationUrlDataProvider
     *
     * @param string $applePayValidationUrl
     *
     * @return void
     */
    public function testIsValidReturnsFalseForUrlOutsideConfiguredApplePayGatewayHosts(string $applePayValidationUrl): void
    {
        $applePayValidationUrlValidator = $this->createApplePayValidationUrlValidator();

        $isValid = $applePayValidationUrlValidator->isValid($applePayValidationUrl);

        $this->assertFalse($isValid);
    }

    /**
     * @return array<string, array<string>>
     */
    public function unknownApplePayValidationUrlDataProvider(): array
    {
        return [
            'unknown host' => ['https://' . static::ATTACKER_HOST . static::APPLE_PAY_VALIDATION_URL_PATH],
            'empty string' => [''],
        ];
    }

    /**
     * @return \Mollie\Yves\Mollie\Validator\ApplePayValidationUrlValidatorInterface
     */
    protected function createApplePayValidationUrlValidator(): ApplePayValidationUrlValidatorInterface
    {
        return (new MollieFactory())->createApplePayValidationUrlValidator();
    }
}
