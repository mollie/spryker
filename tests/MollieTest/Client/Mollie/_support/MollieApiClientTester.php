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
     * @var array
     */
    protected const MOLLIE_MOCKED_GET_PAYMENT_BY_TRANSACTION_ID = [
        'resource' => 'payment',
        'id' => 'tr_IUDAHSMGnU6qLbRaksas',
        'mode' => 'live',
        'amount' => [
            'value' => '10.00',
            'currency' => 'EUR',
        ],
        'description' => 'Order #12345',
        'sequenceType' => 'oneoff',
        'redirectUrl' => 'https://webshop.example.org/order/12345/',
        'webhookUrl' => 'https://webshop.example.org/payments/webhook/',
        'metadata' => [
            'order_id' => 12345,
        ],
        'profileId' => 'pfl_QkEhN94Ba',
        'status' => 'open',
        'isCancelable' => false,
        'createdAt' => '2024-03-20T09:13:37+00:00',
        'expiresAt' => '2024-03-20T09:28:37+00:00',
        '_links' => [
            'self' => [
                'href' => '...',
                'type' => 'application/hal+json',
            ],
            'checkout' => [
                'href' => 'https://www.mollie.com/checkout/select-method/7UhSN1zuXS',
                'type' => 'text/html',
            ],
            'dashboard' => [
                'href' => 'https://www.mollie.com/dashboard/org_12345678/payments/tr_5B8cwPMGnU6qLbRvo7qEZo',
                'type' => 'text/html',
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

    /**
     * @return array
     */
    public function getPaymentByTransactionId(): array
    {
        return static::MOLLIE_MOCKED_GET_PAYMENT_BY_TRANSACTION_ID;
    }
}
