<?php


namespace Core;


use Closure;
use Core\Support\DumpDieException;
use Core\Support\ViewResponse;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use RuntimeException;
use Throwable;

class RequestHandler
{
    protected array $resolvedControllers = [];

    /**
     * @var Application
     */
    protected Application $app;
    /**
     * @var Router
     */
    protected Router $router;

    public function __construct(Application $app, Router $router)
    {
        $this->app    = $app;
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $routeInfo = $this->router->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, ['Content-Type' => 'text/plain'], 'Method not allowed');
            case Dispatcher::FOUND:
                return $this->handleResponse($this->handleRequest($request, $routeInfo));
        }

        throw new RuntimeException('Something went wrong in routing.');
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
            $params  = array_values($routeInfo[2]);

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
            if ($this->app->enableDebug()) {
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