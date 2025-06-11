<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router
{

    private RequestHandlerFactoryInterface $requestHandlerFactory;
    private Dispatcher $dispatcher;

    public function __construct(
        RequestHandlerFactoryInterface $requestHandlerFactory,
        Dispatcher                     $dispatcher
    )
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->dispatcher = $dispatcher;
    }

    public function getHandler(ServerRequestInterface $request): HandlerAndRequestWithArgsContainer
    {
        $fastRouteResult = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($fastRouteResult[0]) {
            case Dispatcher::FOUND:
                $args = $fastRouteResult[2];
                foreach ($args as $name => $value) {
                    $request = $request->withAttribute($name, $value);
                }

                return new HandlerAndRequestWithArgsContainer(
                    $request,
                    $this->requestHandlerFactory->create($fastRouteResult[1])
                );
            case Dispatcher::NOT_FOUND:
                throw new \Exception('Route not found');
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new \Exception('Method not allowed');
        }

        throw new \Exception('Unexpected fast route result');
    }
}
