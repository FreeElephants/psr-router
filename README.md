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

```
