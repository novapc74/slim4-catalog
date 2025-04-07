<?php

use Slim\App;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

return function (App $app) {
    $app->addBodyParsingMiddleware();

    $app->addRoutingMiddleware();

    $logger = new Logger('app');
    $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../var/log/error.log', 0, Logger::ERROR));

     $app->addErrorMiddleware(true,true,true, $logger);
};
