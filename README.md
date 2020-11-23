# rest-framework

Rest Framework - PHP Framework for ReactPHP Library

## Docs

- [Installation](#installation)
    - [Server Requirements](#server-requirements)
    - [Installing Rest](#installing-rest)
    - [Local Development Server](#local-development-server)
- [Deploy](#deploy)
    - [Using Docker](#using-docker)
    - [Using Supervisor](#using-supervisor)
- Usage(#usage)
    - [Router](#router)
    - [Dependency Injection](#dependency-injection)
        - [Singleton](#singleton)
        - [Resolve an instance from container](#resolve-from-container)
    - [Helpers](#helpers)
        

<a name="installation"></a>
## Installation

<a name="server-requirements"></a>
### Server Requirements

The Rest framework has a few system requirements.

- PHP >= 7.4
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

<a name="installing-rest"></a>
### Installing Rest

Rest utilizes [Composer](https://getcomposer.org) to manage its dependencies. So, before using Rest, make sure you have Composer installed on your machine.

Install Rest by issuing the Composer `create-project` command in your terminal:

    composer create-project --prefer-dist nddcoder/rest blog

<a name="local-development-server"></a>

### Local Development Server

Just run `index.php` file

```
php index.php
```

Or, if you have nodemon installed, you can have auto reload feature by using nodemon

```
nodemon index.php
```

<a name="deploy"></a>
## Deploy

<a name="using-docker"></a>
### Using Docker

Just build image based on project's `Dockerfile`

```
docker build -t <image_name>:<image_tag> .
docker run -d \
           -p 8080:8080 \
           -e APP_PORT="0.0.0.0:8080" \
           <image_name>:<image_tag> 
```

<a name="using-supervisor"></a>
### Using Supervisor

```
[program:app-name]
process_name=%(program_name)s_%(process_num)02d
command=php /paht/to/project/index.php
autostart=true
autorestart=true
```

<a name="usage"></a>
## Usage

<a name="router"></a>
### Router

Rest using [Fast Route](https://github.com/nikic/FastRoute) for routing. Application routes can be register in `app/Router.php`.

```php
class Router extends BaseRouter
{
    protected function register(RouteCollector $routes): void
    {
        $routes->get('/', HomeController::class); //using invokeable controller
        $routes->get('/home', [HomeController::class, 'home']);
        $routes->get('/hello/{name}', [HomeController::class, 'hello']);
        
        /*
        $routes->post(...)
        $routes->put(...)
        $routes->delete(...)
        */
    }
}
```

<a name="controller"></a>
### Controller

The first parameter of controller method always is `ServerRequestInterface`, any route params will following this.

```php
class HomeController
{
    public function __invoke(ServerRequestInterface $request)
    {
        $frameworkVersion = Application::VERSION;
        return view('home.twig', compact('frameworkVersion'));
    }

    public function home()
    {
        return response()->redirect('/');
    }
    
    public function hello(ServerRequestInterface $request, $name)
    {
        return "Hello $name";
    }
}
```

<a name="dependency-injection"></a>
### Dependency Injection

Bind class to container

```php
Application::getInstance()
    ->onBoot(function (Application $app) {
        $app->bind(ProductServiceInterface::class, fn($app) => new ProductServiceImpl());
    })
```

<a name="singleton"></a>
#### Singleton

To make a class is singleton, you can make class implemnt `Singleton` interface or bind to container using `singleton` method

Bind class to container

```php

use Rest\Contracts\Singleton;

class SlackService extends Singleton {

}

//or

Application::getInstance()
    ->onBoot(function (Application $app) {
        $app->singleton(SlackService::class, SlackService::class);
    })
```

<a name="resolve-from-container"></a>
#### Resolve an instance from container

Inject dependencies in `__construct` function

```php
class ProductController
{
    protected ProductServiceInterface $productService;
    
    public function __construct(ProductServiceInterface $service)
    {
        $this->productService = $service;
    }
}
```

Using `app` helper

Inject dependencies in `__construct` function

```php
class ProductController
{
    public function __construct()
    {
        $this->productService = app(ProductServiceInterface::class);
        //or
        $this->productService = app()->make(ProductServiceInterface::class);
    }
}
```

<a name="helpers"></a>
### Helpers

`app`: return Application instance or resolve an instance from container

```php
$application = app(); //return Application instance
$classInstace = app(ClassName::class); //ClassName instance
```

`view`: return `ViewResponse` instance. Accept view name and optional data need pass to view

```php
class ProductController
{
    public function index()
    {
        $products = [
            //...
        ];
        
        return view('products.index', [
            'products' => $products
        ]);
    }
}
```

`response`: return `Response` instance, use to build response

```php
class ProductController
{
    public function index()
    {
        $products = [
            //...
        ];
        
        return response()->json([
           'data' => $products
        ]);
    }
}
```

`env`: get enviroment vairable from `$_ENV`. Rest using `vlucas/phpdotenv` for load env variables from `.env` file.

```php
$debug = env('APP_DEBUG') == 'true';
```

`dd`: `var_dump and die` variable for debugging. 

```php
dd($var1, $var2, $var3);
```

`abort`: intermediately return http response. Accept `status code` and `message`

```php
abort(500);
abort(403, 'Permission Denied');
```

`abort_if`: `abort` based on `$condition`

```php
abort_if($condition, $status, $messagge);
```

`abort_unless`: reversed side of `abort_if`

```php
abort_unless($condition, $status, $messagge);
```

`logger`: log a string or an `Throwable` instance to console

```php
logger('Error occurred');
logger($exeption);

```
