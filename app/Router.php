<?php


namespace App;


use App\Controllers\Api\ApiController;
use App\Controllers\HomeController;
use FastRoute\RouteCollector;
use Rest\Router as BaseRouter;

class Router extends BaseRouter
{
    protected function register(RouteCollector $routes): void
    {
        $routes->get('/', HomeController::class);
        $routes->get('/home', [HomeController::class, 'home']);
    }
}