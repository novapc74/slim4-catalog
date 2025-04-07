<?php

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env');
$dotenv->load();

try {
    $container = (new ContainerBuilder())
        ->addDefinitions(__DIR__ . '/container.php')
        ->build();

    return $container->get(App::class);

} catch (Exception $exception) {
}

