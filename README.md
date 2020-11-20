# rest-framework

Rest Framework - PHP Framework for ReactPHP Library

## Docs

- [Installation](#installation)
    - [Server Requirements](#server-requirements)
    - [Installing Rest](#installing-rest)
    - [Local Development Server](#local-development-server)
- [Deploy](#deploy)

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

### Using Docker

Just build image based on project's `Dockerfile`

```
docker build -t <image_name>:<image_tag> .
docker run -d \
           -p 8080:8080 \
           -e APP_PORT="0.0.0.0:8080" \
           <image_name>:<image_tag> 
```

### Using Supervisor

```
[program:app-name]
process_name=%(program_name)s_%(process_num)02d
command=php /paht/to/project/index.php
autostart=true
autorestart=true
```

<a name="router"></a>
## Router

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
## Controller

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
## Dependency Injection

Only `__construct` function can inject dependencies

```php
class ProductController
{
    protected ProductService $productService;
    
    public function __construct(ProductService $service)
    {
        $this->productService = $service;
    }
}
```
