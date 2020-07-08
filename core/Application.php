<?php

namespace Core;

use Core\Exceptions\Handler;
use Core\Middleware\RequestBodyJsonParserMiddleware;
use Exception;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Application
{
    protected static $app;
    public Environment $view;
    public Handler $exceptionHandler;
    protected array $middleware = [];
    protected LoopInterface $loop;
    protected HttpServer $server;
    protected string $port = '3408';
    protected bool $debug = false;
    protected string $viewPath = '';
    protected string $cachePath = '';
    protected Router $router;

    public function __construct()
    {
        $this->loop             = Factory::create();
        $this->debug            = env('APP_DEBUG') == 'true';
        $this->middleware       = [
            new RequestBodyJsonParserMiddleware()
        ];
        $this->exceptionHandler = new Handler();
    }

    public static function getInstance(): self
    {
        if (self::$app) {
            return self::$app;
        }

        return self::$app = new static();
    }

    public function isDebug(): bool
    {
        return $this->debug;
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

    public function router(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    public function exceptionHandler(Handler $handler): self
    {
        $this->exceptionHandler = $handler;
        return $this;
    }

    public function addMiddleware(callable $handler): self
    {
        $this->middleware[] = $handler;
        return $this;
    }

    public function run(): void
    {
        $this->view = new Environment(new FilesystemLoader($this->viewPath), [
            'cache' => $this->cachePath.'/views',
            'debug' => $this->debug
        ]);

        $this->middleware[] = new RequestHandler($this, $this->router ?? new Router());

        $this->server = new HttpServer($this->middleware);

        $this->server->on('error', function (Exception $e) {
            logger('Error: '.$e->getMessage().PHP_EOL);
            if ($e->getPrevious() !== null) {
                logger('Previous: '.$e->getPrevious()->getMessage().PHP_EOL.$e->getPrevious()->getTraceAsString());
            }
        });

        $socketServer  = new SocketServer($this->port, $this->loop);
        $serverAddress = str_replace('tcp://', 'http://', $socketServer->getAddress());
        $this->server->listen($socketServer);
        echo "Listening on {$serverAddress}".PHP_EOL;
        $this->loop->run();
    }
}