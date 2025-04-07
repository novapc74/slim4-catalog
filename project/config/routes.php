<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Action\Category\CategoryAction;
use App\Action\Category\MainCategoryAction;

return function (App $app): void {
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/category', MainCategoryAction::class);
        $group->get('/category/{slug}', CategoryAction::class);
    });
};
