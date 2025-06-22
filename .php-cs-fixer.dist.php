<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        './src/',
        './tests/',
    ]);

return \FreeElephants\PhpCsFixer\build_config($finder);
