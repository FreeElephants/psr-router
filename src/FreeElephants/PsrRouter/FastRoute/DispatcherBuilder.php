<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FreeElephants\PsrRouter\OptionsHandlerInterface;
use FreeElephants\PsrRouter\PathNormalization\Dummy;
use FreeElephants\PsrRouter\PathNormalization\PathNormalizerInterface;
use function FastRoute\simpleDispatcher;

class DispatcherBuilder
{
    private array $routes = [];
    private PathNormalizerInterface $pathNormalizer;
    private OptionsHandlerInterface $optionsHandlerPrototype;

    public function __construct(
        PathNormalizerInterface $pathNormalizer = null
    )
    {
        $this->pathNormalizer = $pathNormalizer ?? new Dummy();
    }

    public function setOptionsHandlerPrototype(OptionsHandlerInterface $handler): self
    {
        $this->optionsHandlerPrototype = $handler;
    }

    public function addRoute(string $path, string $handler, string $method = 'GET'): self
    {
        $path = $this->pathNormalizer->normalizePath($path);

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
                $httpMethods = [];
                if (is_array($methodsOrHandler)) {
                    foreach ($methodsOrHandler as $method => $handler) {
                        $routeCollector->addRoute($method, $path, $handler);
                        $httpMethods[] = $method;
                    }
                } else {
                    $routeCollector->addRoute('GET', $path, $methodsOrHandler);
                }

                if (isset($this->optionsHandlerPrototype) && !in_array('OPTIONS', $httpMethods)) {
                    $allowedMethods = $httpMethods;
                    $allowedMethods[] = 'OPTIONS';
                    $optionsHandler = clone $this->optionsHandlerPrototype;
                    $optionsHandler->setAllowedMethods($allowedMethods);
                    $routeCollector->addRoute(['OPTIONS'], $path, $optionsHandler);
                }
            }
        });
    }

    private function normalizeConfig(array $config): array
    {
        $normalized = [];
        foreach ($config as $path => $methods) {
            $path = $this->pathNormalizer->normalizePath($path);

            if (!is_array($methods)) {
                $methods = ['GET' => $methods];
            }
            $normalized[$path] = $methods;
        }

        return $normalized;
    }
}
