<?php

/**
 * Copy over the following configs to your config
 */
use Mollie\Shared\Mollie\MollieConstants;

$config[MollieConstants::MOLLIE] = [
  MollieConstants::MOLLIE_PROFILE_ID => '',
  MollieConstants::MOLLIE_TEST_MODE => true,
  MollieConstants::MOLLIE_SCRIPT_SOURCE => '',
];
