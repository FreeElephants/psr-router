<?php

namespace FreeElephants\PsrRouter;

use Psr\Http\Server\RequestHandlerInterface;

interface OptionsHandlerInterface extends RequestHandlerInterface
{
    public function setAllowedMethods(array $httpMethods): void;
}
