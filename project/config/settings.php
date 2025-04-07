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
];

return $settings;
