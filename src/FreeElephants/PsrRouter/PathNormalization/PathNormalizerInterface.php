<?php

namespace FreeElephants\PsrRouter\PathNormalization;

interface PathNormalizerInterface
{
    public function normalizePath(string $path): string;
}
