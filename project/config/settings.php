<?php

// Should be set to 0 in production
error_reporting(E_ALL);

// Should be set to '0' in production
ini_set('display_errors', '1');

$settings = [
    'database' => [
        'host' => env('DATABASE_HOST'),
        'name' => env('DATABASE_NAME'),
        'user' => env('DATABASE_USER'),
        'password' => env('DATABASE_PASSWORD'),
    ],
    'cache' => [
        'enabled' => env('CACHE_ENABLED', true),
        'host' => env('REDIS_HOST', 'localhost'),
        'port' => env('REDIS_PORT', 6379),
        'password' => env('REDIS_PASS', 'forge'),
        'url' => env('REDIS_URL', null),
    ]
];

return $settings;
