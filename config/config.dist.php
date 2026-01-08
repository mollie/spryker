<?php

/**
 * Mollie Payment Configuration Blueprint
 * Copy the following blueprint to your config file (e.g., config/Shared/config_default.php)
 * and fill in your Mollie credentials.
 */

use Mollie\Shared\Mollie\MollieConfig;
use Mollie\Shared\Mollie\MollieConstants;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Shared\Oms\OmsConstants;
use Spryker\Shared\Sales\SalesConstants;

$config[MollieConstants::MOLLIE] = [
  MollieConstants::MOLLIE_PROFILE_ID => '',
  MollieConstants::MOLLIE_TEST_MODE => true,
];

$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    APPLICATION_VENDOR_DIR . '/mollie/spryker-payment/config/Zed/Oms',
];

$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    MollieConfig::MOLLIE_PAYMENT_CREDIT_CARD => 'MollieCreditCardPayment',
];

$config[OmsConstants::ACTIVE_PROCESSES] = [
    'MollieCreditCardPayment',
];
