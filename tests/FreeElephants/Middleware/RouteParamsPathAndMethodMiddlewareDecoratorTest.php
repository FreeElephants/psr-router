<?php

declare(strict_types=1);

namespace FreeElephants\Middleware;

use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteParamsPathAndMethodMiddlewareDecoratorTest extends TestCase
{
    public function testMatch(): void
    {
        $decorator = new RouteParamsPathAndMethodMiddlewareDecorator(
            '/',
            ['GET'],
            new class () implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    return new Response();
                }
            },
        );

        $this->assertTrue($decorator->match(new ServerRequest('GET', '/')));
        $this->assertFalse($decorator->match(new ServerRequest('POST', '/')));
        $this->assertFalse($decorator->match(new ServerRequest('GET', '/foo')));
    }

    public function testMatchWithArgsInPath(): void
    {
        $decorator = new RouteParamsPathAndMethodMiddlewareDecorator(
            '/users/{userId}',
            ['GET'],
            new class () implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    return new Response();
                }
            },
        );

        $this->assertTrue($decorator->match(new ServerRequest('GET', '/users/some-id')));
        $this->assertFalse($decorator->match(new ServerRequest('POST', '/users/some-id')));
        $this->assertFalse($decorator->match(new ServerRequest('POST', '/')));
        $this->assertFalse($decorator->match(new ServerRequest('GET', '/foo/bar')));
    }
}
