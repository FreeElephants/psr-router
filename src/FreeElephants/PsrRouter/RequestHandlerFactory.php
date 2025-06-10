<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerFactory implements RequestHandlerFactoryInterface
{

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(string $key): RequestHandlerInterface
    {
        return $this->container->get($key);
    }
}
