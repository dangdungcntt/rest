<?php

use App\Router;
use Rest\Application;

require_once(__DIR__ . '/bootstrap.php');

Application::getInstance()
    ->viewPath(env('VIEW_PATH', __DIR__ . '/resources/views'))
    ->cachePath(env('CACHE_PATH', __DIR__ . '/storage/cache'))
    ->listen(env('APP_URI', '127.0.0.1:3408'))
    ->router(new Router())
//    ->addMiddleware(new \Sikei\React\Http\Middleware\CorsMiddleware([
//        'allow_origin' => [
//            env('CORS_ALLOWED_ORIGIN', '*')
//        ],
//    ]))
    ->onBoot(function (Application $app) {
        //Bind to container here: $app->bind(...)
        //Bind singleton to container here: $app->singleton(...) or implement Singleton interface
    })
    ->run();
