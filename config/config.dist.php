<?php

declare(strict_types = 1);

/**
 * Mollie Payment Configuration Blueprint
 * Copy the following blueprint to your config file (e.g., config/Shared/config_default.php)
 * and fill in your Mollie credentials.
 */

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Spryker\Shared\Oms\OmsConstants;
use Spryker\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\OmsConfig;

$config[MollieConstants::MOLLIE] = [
    MollieConstants::MOLLIE_PROFILE_ID => '',
    MollieConstants::MOLLIE_TEST_MODE => true,
    MollieConstants::MOLLIE_API_KEY => '',
    MollieConstants::MOLLIE_DEBUG_MODE => '',
    MollieConstants::MOLLIE_REDIRECT_URL => '',
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_ENABLED => true,
    MollieConstants::MOLLIE_CREDIT_CARD_COMPONENTS_JS_SRC => '', //Example: https://js.mollie.com/v1/mollie.js
    MollieConstants::MOLLIE_WEBHOOK_URL => '',
    MollieConstants::MOLLIE_TEST_ENVIRONMENT_WEBHOOK_URL => '',
    MollieConstants::MOLLIE_OMS_TO_PAYMENT_METHOD_MAPPING => [
        'mollieCreditCardPayment' => 'creditcard',
    ],
     MollieConstants::MOLLIE_PAYMENT_METHOD_MANUAL_CAPTURE => [
        'creditcard',
        'klarna',
    ],
     MollieConstants::MOLLIE_INCLUDE_WALLETS => ['applepay'],
];
$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    APPLICATION_VENDOR_DIR . '/mollie/spryker-payment/config/Zed/Oms',
];

$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD => 'MolliePaymentStateMachine01',
    MollieConfig::MOLLIE_PAYMENT_KLARNA => 'MolliePaymentStateMachineManualCapture01',
];

$config[OmsConstants::ACTIVE_PROCESSES] = [
    'MolliePaymentStateMachine01',
];
