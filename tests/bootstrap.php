<?php

use Spryker\Shared\Config\Config;

if (!defined('APPLICATION_ROOT_DIR')) {
    define('APPLICATION_ROOT_DIR', __DIR__ . '/../App/');
}
if (!defined('APPLICATION_STORE')) {
    define('APPLICATION_STORE', 'DE');
}
if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'dev');
}

$config = Config::getInstance();
$config->init();
