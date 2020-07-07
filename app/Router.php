<?php


namespace App;


use App\Controllers\Api\ApiController;
use App\Controllers\HomeController;
use Core\Router as CoreRouter;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;

class Router extends CoreRouter
{
    protected function register(RouteCollector $routes): void
    {
        $routes->get('/', HomeController::class);
        $routes->get('/home', [HomeController::class, 'home']);
        $routes->get('/api', [ApiController::class, 'index']);
        $routes->get('/hello/{name}', function (ServerRequestInterface $request, $name) {
            return "Hello $name";
        });
    }
}