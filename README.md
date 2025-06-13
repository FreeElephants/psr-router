# Framework Agnostic PSR Router

## Usage

```php

$routes = [
    // 1. "flat" syntax - single level configuration
    '/api/v1/users' => GetUsersHandler::class, // by default GET method ar user for single value
    // 2. Method-based syntax - two levels configuration
    '/api/v1/users/{id}' => [ // Router parse path params and set named arguments to request (method based syntax
        'GET' => GetUserHandler::class,
        'PATCH' => UpdateUserHandler::class, 
    ],
];

$fastRouteDispatcher = (new \FreeElephants\PsrRouter\FastRoute\DispatcherBuilder())
    ->addConfig($routes)
    ->addRoute('/foo', FooHandler::class) // You can define routes by one
    ->build(); 

$router = new \FreeElephants\PsrRouter\Router(
    new \FreeElephants\PsrRouter\RequestHandlerFactory($psrContainer),
    $fastRouteDispatcher,
);

```

## Customization

### Slash Normalizing

For handle trailing slashes in path you can inject PathNormalizer implementation into both classes:  
- Router - its rtrim trailing slash at runtime from request path.  
- DispatcherBuilder - its rtrim on add route at build time.

```php
$pathNormalizer = new \FreeElephants\PsrRouter\PathNormalization\TrailingSlashTrimmer();
$dispatcher = (new \FreeElephants\PsrRouter\FastRoute\DispatcherBuilder(
    pathNormalizer: $pathNormalizer))
    ...
    ...
    ->build();


$router = new \FreeElephants\PsrRouter\Router(
    $dispatcher,
    $requestHandlerFactory,
    pathNormalizer: $pathNormalizer
    
)
```

### OPTIONS Handling  

By default, Router does not know about `OPTIONS` and allowed methods for route. 

For handle `OPTIONS` request, you can set your own handler prototype. Every route will be handling this method according to other collected methods.  

```php
/**
 * @var \FreeElephants\PsrRouter\FastRoute\DispatcherBuilder $dispatcherBuilder
 */
$dispatcherBuilder->setOptionsHandlerPrototype($optionsRequestHandlerImpl)->build();
```
