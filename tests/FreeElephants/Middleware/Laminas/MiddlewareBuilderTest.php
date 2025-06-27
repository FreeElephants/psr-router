<?php

declare(strict_types=1);

namespace FreeElephants\Middleware\Laminas;

use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $container = new class () implements ContainerInterface {
            public function get(string $id)
            {
                return new class ($id) implements MiddlewareInterface {
                    private string $id;

                    public function __construct(string $id)
                    {
                        $this->id = $id;
                    }

                    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                    {
                        return $handler->handle($request)->withAddedHeader('handled-with', $this->id);
                    }
                };
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        $builder = new MiddlewareBuilder($container);
        $middleware = $builder
            ->addConfig([
                '/' => 'root-middleware',
            ])
            ->addConfig([
                '/' => [
                    'additional-root-middleware-A',
                    'additional-root-middleware-B',
                ],
            ])
            ->addConfig([
                '/' => [
                    'POST' => 'additional-root-post-middleware',
                ],
            ])
            ->addConfig([
                '/poster' => [
                    'POST' => 'poster-middleware-1',
                ],
            ])->addConfig([
                '/poster' => [
                    'POST' => 'poster-middleware-2',
                ],
            ])->addConfig([
                '/putter/nested' => [
                    'PUT' => [
                        'putter-middleware-1',
                        'putter-middleware-2',
                    ],
                ],
            ])->addConfig([
                '/{firstLevel}' => [
                    'OPTIONS' => [
                        'common-middleware',
                    ],
                ],
            ])
            ->build()
        ;

        $simpleHandler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };

        $responseForGetRoot = $middleware->process(new ServerRequest('GET', '/'), $simpleHandler);
        $this->assertCount(3, $responseForGetRoot->getHeader('handled-with'));

        $responseForPostRoot = $middleware->process(new ServerRequest('POST', '/'), $simpleHandler);
        $this->assertCount(4, $responseForPostRoot->getHeader('handled-with'));

        $responseForPostPoster = $middleware->process(new ServerRequest('POST', '/poster'), $simpleHandler);
        $this->assertCount(2, $responseForPostPoster->getHeader('handled-with'));

        $responseUnmatched = $middleware->process(new ServerRequest('GET', '/non-configured-path'), $simpleHandler);
        $this->assertCount(0, $responseUnmatched->getHeader('handled-with'));

        $optionsResponse = $middleware->process(new ServerRequest('OPTIONS', '/firstLevelPath'), $simpleHandler);
        $this->assertCount(1, $optionsResponse->getHeader('handled-with'));
    }
}
