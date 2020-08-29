<?php

use App\Router;
use Core\Application;

require_once(__DIR__ . '/bootstrap.php');

Application::getInstance()
    ->viewPath(env('VIEW_PATH', __DIR__ . '/resources/views'))
    ->cachePath(env('CACHE_PATH', __DIR__ . '/storage/cache'))
    ->listen(env('APP_PORT', 3408))
    ->router(new Router())
    ->onBoot(function (Application $app) {
        //Bind to container here: $app->bind(...)
        //Bind singleton to container here: $app->singleton(...) or implement Singleton interface
    })
    ->run();
