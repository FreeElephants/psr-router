<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router
{

    public function __construct()
    {
    }

    public function getHandler(ServerRequestInterface  $request): RequestHandlerInterface
    {

    }
}
