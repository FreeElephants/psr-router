<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class DispatcherBuilder
{
    private array $routes = [];

    public function addRoute(string $path, string $handler, string $method = 'GET'): self
    {
        $this->routes[$path][$method] = $handler;

        return $this;
    }

    public function addConfig(array $config): self
    {
        $this->routes = array_merge_recursive($this->routes, $this->normalizeConfig($config));

        return $this;
    }

    public function build(): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $routeCollector) {
            foreach ($this->routes as $path => $methodsOrHandler) {
                if(is_array($methodsOrHandler)) {
                    foreach ($methodsOrHandler as $method => $handler) {
                        $routeCollector->addRoute($method, $path, $handler);
                    }
                } else {
                    $routeCollector->addRoute('GET', $path, $methodsOrHandler);
                }
            }
        });
    }

    private function normalizeConfig(array $config): array
    {
        $normalized = [];
        foreach ($config as $path => $methods) {
            if(!is_array($methods)) {
                $methods = ['GET' => $methods];
            }
            $normalized[$path] = $methods;
        }

        return $normalized;
    }
}
