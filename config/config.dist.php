<?php

/**
 * Mollie Payment Configuration Blueprint
 * Copy the following blueprint to your config file (e.g., config/Shared/config_default.php)
 * and fill in your Mollie credentials.
 */
use Mollie\Shared\Mollie\MollieConstants;

$config[MollieConstants::MOLLIE] = [
  MollieConstants::MOLLIE_PROFILE_ID => '',
  MollieConstants::MOLLIE_TEST_MODE => true,
];
