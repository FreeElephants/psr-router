<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\Exception;

class MethodNotAllowed extends \RuntimeException implements ExceptionInterface
{
    private array $allowedMethods;

    public function __construct(array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
        parent::__construct('Method not allowed. Allowed methods are: ' . implode(', ', $allowedMethods));
    }

    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
