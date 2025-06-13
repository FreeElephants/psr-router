<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter;

use FastRoute\RouteCollector;
use FreeElephants\PsrRouter\PathNormalization\TrailingSlashTrimmer;
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

            $r->addRoute('GET', '/users', 'get users handler');
        });

        $requestHandlerFactory = $this->createMock(RequestHandlerFactoryInterface::class);
        $expectedRequestHandler = $this->createMock(RequestHandlerInterface::class);
        $requestHandlerFactory->method('create')->willReturn($expectedRequestHandler);

        $router = new Router(
            $dispatcher,
            $requestHandlerFactory,
            null,
            null,
            new TrailingSlashTrimmer(),
        );

        $serverRequestForGetUserWithId100 = new ServerRequest('GET', '/users/100');
        $actualRequestHandlerAndRequest = $router->getHandler($serverRequestForGetUserWithId100);
        $this->assertSame($expectedRequestHandler, $actualRequestHandlerAndRequest->getHandler());
        $this->assertSame('100', $actualRequestHandlerAndRequest->getRequest()->getAttribute('id'));


        $serverRequestForGetUsersWithoutSlash = new ServerRequest('GET', '/users');
        $actualRequestHandlerAndRequest = $router->getHandler($serverRequestForGetUsersWithoutSlash);
        $this->assertSame($expectedRequestHandler, $actualRequestHandlerAndRequest->getHandler());

        $serverRequestForGetUsersWithSlash = new ServerRequest('GET', '/users/');
        $actualRequestHandlerAndRequest = $router->getHandler($serverRequestForGetUsersWithSlash);
        $this->assertSame($expectedRequestHandler, $actualRequestHandlerAndRequest->getHandler());

        $serverRequestForGetUserWithWrongId = new ServerRequest('GET', '/users/bla-bla');
        $this->expectException(\Exception::class);
        $router->getHandler($serverRequestForGetUserWithWrongId);
    }
}
