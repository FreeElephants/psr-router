<?php

namespace FreeElephants\PsrRouter\PathNormalization;

class Dummy implements PathNormalizerInterface
{
    public function normalizePath(string $path): string
    {
        return $path;
    }
}
