<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use FastRoute\Dispatcher;
use FreeElephants\PsrRouter\Exception\MethodNotAllowed;
use FreeElephants\PsrRouter\Exception\NotFound;
use FreeElephants\PsrRouter\PathNormalization\Dummy;
use FreeElephants\PsrRouter\PathNormalization\PathNormalizerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router
{

    private RequestHandlerFactoryInterface $requestHandlerFactory;
    private Dispatcher $dispatcher;
    private PathNormalizerInterface $pathNormalizer;
    private ?RequestHandlerInterface $notFoundHandler;
    private ?MethodNotAllowedHandlerInterface $methodNotAllowedHandler;

    public function __construct(
        Dispatcher                       $dispatcher,
        RequestHandlerFactoryInterface   $requestHandlerFactory,
        RequestHandlerInterface          $notFoundHandler = null,
        MethodNotAllowedHandlerInterface $methodNotAllowedHandler = null,
        PathNormalizerInterface          $pathNormalizer = null
    )
    {
        $this->dispatcher = $dispatcher;
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->notFoundHandler = $notFoundHandler;
        $this->methodNotAllowedHandler = $methodNotAllowedHandler;
        $this->pathNormalizer = $pathNormalizer ?? new Dummy();
    }

    public function getHandler(ServerRequestInterface $request): HandlerAndRequestWithArgsContainer
    {
        $path = $this->pathNormalizer->normalizePath($request->getUri()->getPath());
        $fastRouteResult = $this->dispatcher->dispatch($request->getMethod(), $path);
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
                if (isset($this->notFoundHandler)) {
                    return new HandlerAndRequestWithArgsContainer($request, $this->notFoundHandler);
                }

                throw new NotFound();
            case Dispatcher::METHOD_NOT_ALLOWED:
                if (isset($this->methodNotAllowedHandler)) {
                    return new HandlerAndRequestWithArgsContainer($request, $this->methodNotAllowedHandler);
                }

                throw new MethodNotAllowed($request, $fastRouteResult[1]);
        }

        throw new \Exception('Unexpected fast route result');
    }
}
