<?php

namespace App;

use App\Controllers\Api\ApiController;
use App\Controllers\HomeController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router
{
    public function load(): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $routes) {
            $routes->get('/', HomeController::class);
            $routes->get('/home', [HomeController::class, 'home']);
            $routes->get('/api', [ApiController::class, 'index']);
        });
    }
}