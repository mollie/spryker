<?php

declare(strict_types=1);

namespace MollieTest\Client\Mollie;

use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class MollieApiClientTester extends Actor
{
    use _generated\MollieApiClientTesterActions;

    /**
     * @var array
     */
    protected const MOLLIE_MOCKED_PAYMENT_METHOD_RESPONSE_PAYLOAD = [
       'count' => 2,
        '_embedded' => [
            'methods' => [
                [
                    'resource' => 'method',
                    'id' => 'ideal',
                    'description' => 'iDEAL',
                    'minimumAmount' => [
                        'value' => '0.01',
                        'currency' => 'EUR',
                    ],
                    'maximumAmount' => [
                        'value' => '50000.00',
                        'currency' => 'EUR',
                    ],
                    'image' => [
                        'size1x' => 'https://mollie.com/external/icons/payment-methods/ideal.png',
                        'size2x' => 'https://mollie.com/external/icons/payment-methods/ideal%402x.png',
                        'svg' => 'https://mollie.com/external/icons/payment-methods/ideal.svg',
                    ],
                    'status' => 'activated',
                    '_links' => [
                        'self' => [
                            'href' => '...',
                            'type' => 'application/hal+json',
                        ],
                    ],
                ],
                [
                    'resource' => 'method',
                    'id' => 'creditcard',
                    'description' => 'Credit card',
                    'minimumAmount' => [
                        'value' => '0.01',
                        'currency' => 'EUR',
                    ],
                    'maximumAmount' => [
                        'value' => '2000.00',
                        'currency' => 'EUR',
                    ],
                    'image' => [
                        'size1x' => 'https://mollie.com/external/icons/payment-methods/creditcard.png',
                        'size2x' => 'https://mollie.com/external/icons/payment-methods/creditcard%402x.png',
                        'svg' => 'https://mollie.com/external/icons/payment-methods/creditcard.svg',
                    ],
                    'status' => 'activated',
                    '_links' => [
                        'self' => [
                            'href' => '...',
                            'type' => 'application/hal+json',
                        ],
                    ],
                ],
            ],
        ],
        '_links' => [
            'self' => [
                'href' => '...',
                'type' => 'application/hal+json',
            ],
            'documentation' => [
                'href' => '...',
                'type' => 'text/html',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function getMollieMockedPaymentMethodResponsePayload(): array
    {
        return static::MOLLIE_MOCKED_PAYMENT_METHOD_RESPONSE_PAYLOAD;
    }
}
