<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Настраиваем Eloquent
        $settings = $container->get('settings');
        (require __DIR__ . '/eloquent.php')($settings);

        // Регистрируем маршруты
        (require __DIR__ . '/routes.php')($app);

        // Регистрируем промежуточное ПО
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },
];
