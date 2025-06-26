<?php

declare(strict_types=1);

namespace FreeElephants\Middleware\Laminas;

use FreeElephants\Middleware\RouteParamsPathAndMethodMiddlewareDecorator;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewareBuilder
{
    private array $config;
    private ContainerInterface $container;
    private array $defaultMethods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ];

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    public function addConfig(array $config): self
    {
        foreach ($config as $path => $pathConfig) {
            if (is_string($pathConfig)) {
                foreach ($this->defaultMethods as $method) {
                    $this->config[$path][$method][] = $pathConfig;
                }
            } elseif (is_array($pathConfig)) {
                foreach ($pathConfig as $middlewareClassName) {
                    foreach ($this->defaultMethods as $method) {
                        $this->config[$path][$method][] = $middlewareClassName;
                    }
                }
            }
        }

        return $this;
    }

    public function build(): MiddlewareInterface
    {
        $pipe = new MiddlewarePipe();

        foreach ($this->config as $path => $pathConfig) {
            foreach ($pathConfig as $method => $middlewares) {
                foreach ($middlewares as $middlewareClassName) {
                    $pipe->pipe(new RouteParamsPathAndMethodMiddlewareDecorator($path, [$method], $this->container->get($middlewareClassName)));
                }
            }
        }

        return $pipe;
    }
}
