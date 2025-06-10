<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testGetHandler(): void
    {
        $router = new Router();
        try {

            $router->getHandler(new ServerRequest('GET', '/'));
        } catch (\TypeError $e) {
            
        }
    }
}
