<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class DispatcherBuilder
{
    private array $routes = [];

    public function addRoute(string $path, string $method, string $handler): self
    {
        $this->routes[$path][$method] = $handler;

        return $this;
    }

    public function addConfig(array $config): self
    {
        $this->routes = array_merge_recursive($this->routes, $config);

        return $this;
    }

    public function build(): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $routeCollector) {
            foreach ($this->routes as $path => $methods) {
                foreach ($methods as $method => $handler) {
                    $routeCollector->addRoute($method, $path, $handler);
                }
            }
        });
    }
}
