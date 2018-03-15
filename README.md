# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)![Build Status](https://travis-ci.org/igniphp/framework.svg?branch=master)

Igni is a mini php7 framework with modular architecture support that helps you quickly write scalable PSR-7 and PSR-15 compilant REST services.

```php
<?php
require 'vendor/autoload.php';

use Igni\Http\Application;
use Igni\Http\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$application = new Application();

// Routing
$application->get('/hello/{name}', function (Request $request) : Response {
    return Response::fromText("Hello {$request->getAttribute('name')}.");
});

// Middleware
$application->use(function($request, $next) {
    $response = $next->handle($request);
    return $response->withAddedHeader('Version', $this->getConfig()->get('version'));
});

// Modules
$application->extend(new class implements ConfigProvider {
    public function provideConfig(Config $config): void {
        $config->set('version', '1.0');
    }
});

$application->run();
```

## Installation and requirements

Recommended installation way of the Igni Framework is with composer:

``` 
composer install igniphp/framework
```

Requirements:
 - php 7.1 or better
 - [swoole](https://github.com/swoole/swoole-src) extension for build-in http server support

## Features

### Routing

Igni router is based on the fastest routing library (nikic/fastroute).

### PSR-7, PSR-15 Support

Igni fully supports PSR message standards for both manipulating http response, request and http middlwares.

### Dependency Injection and Autoresolving

Igni autoresolves dependencies for you and provides intuitive dependency container. 
It also allows you to use any PSR compatible container of your choice.

### Modular architecture

Modular and scalable solution is one of the most important aspects why this framework was born.
Simply create a module class, implement required interfaces and extend application by your module.

### Performant, production ready http server

No nginx nor apache is required with Igni. Now you can run your php applications the same manner you run node application:
 ``` 
php examples/build_in_server_example.php
 ```
 
Igni http server is as fast as express.js application with almost 0 configuration. 

### Detailed documentation

Detailed documentation and more examples can be [found here](docs/README.md) and in examples directory.
