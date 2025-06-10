<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testGetHandler(): void
    {
        $router = new Router();
        $router->getHandler();
    }
}
