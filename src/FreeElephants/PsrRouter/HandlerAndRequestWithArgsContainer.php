<?php

namespace FreeElephants\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandlerAndRequestWithArgsContainer
{
    private ServerRequestInterface $request;
    private RequestHandlerInterface $handler;

    public function __construct(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    )
    {

        $this->request = $request;
        $this->handler = $handler;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }
}
