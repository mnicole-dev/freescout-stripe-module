<?php

if (!defined('STRIPE_MODULE_DIR')) {
    define('STRIPE_MODULE_DIR', __DIR__);
}

require_once __DIR__ . '/vendor/autoload.php';

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}
