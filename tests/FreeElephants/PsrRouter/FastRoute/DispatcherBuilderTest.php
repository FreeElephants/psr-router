<?php

declare(strict_types=1);

namespace FreeElephants\PsrRouter\FastRoute;

use FastRoute\Dispatcher;
use FreeElephants\PsrRouter\PathNormalization\TrailingSlashTrimmer;
use PHPUnit\Framework\TestCase;

class DispatcherBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = new DispatcherBuilder(new TrailingSlashTrimmer());

        $dispatcher = $builder
            ->addRoute('/users/{id}', 'get user by id')
            ->addConfig([
                '/users/{id}' => [
                    'PATCH'  => 'update user',
                    'DELETE' => 'delete user',
                ],
                '/users'      => 'get users',
            ])
            ->addConfig([
                '/users/' => [ // trailing slash will be normalized with given PathNormalizerInterface
                    'POST' => 'post user to collection',
                ],
            ])
            ->build()
        ;

        $this->assertSame([
            Dispatcher::FOUND,
            'get user by id',
            ['id' => '123'],
        ], $dispatcher->dispatch('GET', '/users/123'), 'found GET /users/123 handler');

        $this->assertSame([
            Dispatcher::FOUND,
            'delete user',
            ['id' => '124'],
        ], $dispatcher->dispatch('DELETE', '/users/124'), 'found DELETE /users/124 handler');

        $this->assertSame([
            Dispatcher::FOUND,
            'get users',
            [],
        ], $dispatcher->dispatch('GET', '/users'), 'found GET /users handler');

        $this->assertSame([
            Dispatcher::METHOD_NOT_ALLOWED,
            ['GET', 'POST'],
        ], $dispatcher->dispatch('PATCH', '/users'), 'not found PATCH /users handler');

        $this->assertSame([
            Dispatcher::NOT_FOUND,
        ], $dispatcher->dispatch('GET', '/not-found'), 'not found GET /not-found handler');
    }
}
