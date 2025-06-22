<?php

declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use Psr\Http\Server\RequestHandlerInterface;

interface RequestHandlerFactoryInterface
{
    public function create(string $key): RequestHandlerInterface;
}
