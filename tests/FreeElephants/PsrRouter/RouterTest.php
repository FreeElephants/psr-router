<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use FastRoute\RouteCollector;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use function FastRoute\simpleDispatcher;

class RouterTest extends TestCase
{
    public function testGetHandler(): void
    {
        $dispatcher = simpleDispatcher(function(RouteCollector $r) {
            // {id} must be a number (\d+)
            $r->addRoute('GET', '/users/{id:\d+}', 'get_user_handler');
        });

        $requestHandlerFactory = $this->createMock(RequestHandlerFactoryInterface::class);
        $expectedRequestHandler = $this->createMock(RequestHandlerInterface::class);
        $requestHandlerFactory->expects($this->once())->method('create')->with('get_user_handler')->willReturn($expectedRequestHandler);
        $router = new Router(
            $requestHandlerFactory,
            $dispatcher,
        );

        $serverRequest = new ServerRequest('GET', '/users/100');
        $actualRequestHandler = $router->getHandler($serverRequest);
        $this->assertSame($expectedRequestHandler, $actualRequestHandler);
    }
}
