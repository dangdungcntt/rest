<?php

namespace App;

use App\Support\DumpDieException;
use App\Support\ViewResponse;
use Closure;
use Exception;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use RuntimeException;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Application
{
    protected static $app;
    public Environment $view;
    protected LoopInterface $loop;
    protected HttpServer $server;
    protected string $port = '3408';
    protected bool $debug = false;
    protected string $viewPath = '';
    protected string $cachePath = '';
    protected Dispatcher $routerDispatcher;

    protected array $resolvedControllers = [];

    public function __construct()
    {
        $this->loop             = Factory::create();
        $this->routerDispatcher = (new Router())->load();
        $this->debug            = env('APP_DEBUG') == 'true';
    }

    public static function getInstance(): self
    {
        if (self::$app) {
            return self::$app;
        }

        return self::$app = new static();
    }

    public function listen(string $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function viewPath(string $viewPath): self
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    public function cachePath(string $cachePath): self
    {
        $this->cachePath = $cachePath;
        return $this;
    }

    public function run(): void
    {
        $this->view = new Environment(new FilesystemLoader($this->viewPath), [
            'cache' => $this->cachePath.'/views',
            'debug' => $this->debug
        ]);

        $this->server = $this->initHttpServer();

        $this->server->on('error', function (Exception $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;
            if ($e->getPrevious() !== null) {
                echo 'Previous: '.$e->getPrevious()->getMessage().PHP_EOL.$e->getPrevious()->getTraceAsString();
            }
        });

        $socketServer  = new SocketServer($this->port, $this->loop);
        $serverAddress = str_replace('tcp://', 'http://', $socketServer->getAddress());
        $this->server->listen($socketServer);
        echo "Listening on {$serverAddress}".PHP_EOL;
        $this->loop->run();
    }

    protected function initHttpServer(): HttpServer
    {
        return new HttpServer(function (ServerRequestInterface $request) {
            $routeInfo = $this->routerDispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
                case Dispatcher::METHOD_NOT_ALLOWED:
                    return new Response(405, ['Content-Type' => 'text/plain'], 'Method not allowed');
                case Dispatcher::FOUND:
                    return $this->handleResponse($this->handleRequest($request, $routeInfo));
            }

            throw new RuntimeException('Something went wrong in routing.');
        });
    }

    protected function handleResponse($response)
    {
        if ($response instanceof Response) {
            return $response;
        }

        if ($response instanceof ViewResponse) {
            return $response->render();
        }

        if (is_array($response)) {
            return new Response(200, ['Content-type' => 'application/json'], json_encode($response));
        }

        return new Response(200, [], (string) $response);
    }

    protected function handleRequest(ServerRequestInterface $request, array $routeInfo)
    {
        $exceptionMessage = '';
        try {
            $handler = $routeInfo[1];
            $params  = $routeInfo[2];
            if ($handler instanceof Closure) {
                return $handler($request, ...$params);
            }

            [$fqnClass, $method] = $this->parseHandler($handler);

            $controller = $this->resolveController($fqnClass);

            if (!empty($method)) {
                return $controller->{$method}($request, ...$params);
            }

            if (is_callable($controller)) {
                return $controller($request, ...$params);
            }

            throw new RuntimeException(sprintf("Class %s is not invokeable", $fqnClass));
        } catch (Throwable $throwable) {
            if ($throwable instanceof DumpDieException) {
                return new Response(200, ['Content-Type' => 'text/plain'],
                    $throwable->getMessage());
            }

            $exceptionMessage = $throwable->getMessage().PHP_EOL.$throwable->getTraceAsString();
            if ($this->debug) {
                return new Response(500, ['Content-Type' => 'text/plain'],
                    $exceptionMessage);
            }
        } finally {
            if ($exceptionMessage) {
                echo $exceptionMessage;
            }
        }
    }

    protected function parseHandler($handler): array
    {
        $fqnClass = '';
        $method   = '';

        if (is_string($handler)) {
            if (str_contains($handler, '@')) {
                [$fqnClass, $method] = explode('@', $handler);
            } else {
                $fqnClass = $handler;
            }
        } elseif (is_array($handler) && count($handler) == 2) {
            $fqnClass = $handler[0];
            $method   = $handler[1];
        }

        return [$fqnClass, $method];
    }

    protected function resolveController($fqnClass)
    {
        if (!empty($this->resolvedControllers[$fqnClass])) {
            return $this->resolvedControllers[$fqnClass];
        }

        $controller = new $fqnClass();

        if ($controller->singleton ?? false) {
            $this->resolvedControllers[$fqnClass] = $controller;
        }

        return $controller;
    }
}