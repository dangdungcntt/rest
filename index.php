<?php

use App\Application;

require_once(__DIR__.'/bootstrap.php');

Application::getInstance()
    ->viewPath(env('VIEW_PATH', __DIR__.'/resources/views'))
    ->cachePath(env('CACHE_PATH', __DIR__.'/cache'))
    ->listen(env('APP_PORT', 3408))
    ->run();
