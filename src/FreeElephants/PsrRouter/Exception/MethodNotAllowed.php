<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\Exception;

use Psr\Http\Message\ServerRequestInterface;

class MethodNotAllowed extends \RuntimeException implements ExceptionInterface
{
    private array $allowedMethods;

    public function __construct(ServerRequestInterface $request, array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
        $allowed = join(', ', $allowedMethods);
        $message = sprintf('Method %s not allowed for route %s. Allowed methods are: %s', $request->getMethod(), $request->getUri(), $allowed);

        parent::__construct($message);
    }

    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
