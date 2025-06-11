<?php

namespace FreeElephants\PsrRouter\PathNormalization;

class TrailingSlashTrimmer implements PathNormalizerInterface
{
    public function normalizePath(string $path): string
    {
        return rtrim($path, '/');
    }
}
