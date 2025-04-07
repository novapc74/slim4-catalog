<?php
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

return function (array $settings): void
{
    $capsule = new Capsule;

    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $settings['database']['host'],
        'database' => $settings['database']['name'],
        'username' => $settings['database']['user'],
        'password' => $settings['database']['password'],
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);

    $capsule->setEventDispatcher(new Dispatcher(new Container));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};
