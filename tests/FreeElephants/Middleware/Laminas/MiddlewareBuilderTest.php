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
            private array $ids = [];

            public function isIdKnown(string $id): bool
            {
                return in_array($id, $this->ids, true);
            }

            public function get(string $id)
            {
                $this->ids[] = $id;

                return new class () implements MiddlewareInterface {
                    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                    {
                        return new Response();
                    }
                };
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        $builder = new MiddlewareBuilder($container);
        $middleware = $builder->addConfig([
            '/' => 'root-middleware',
        ])->addConfig([
            '/' => [
                'additional-root-middleware-A',
                'additional-root-middleware-B',
            ],
        ])->build();
        $middleware->process(new ServerRequest('/', 'GET'), new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        });

        $this->assertTrue($container->isIdKnown('root-middleware'));
    }
}
