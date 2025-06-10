<?php
declare(strict_types=1);

namespace FreeElephants\PsrRouter\FastRoute;

use FastRoute\Dispatcher;
use PHPUnit\Framework\TestCase;

class DispatcherBuilderTest extends TestCase
{

    public function testBuild(): void
    {
        $builder = new DispatcherBuilder();

        $dispatcher = $builder
            ->addRoute('/users/{id}', 'GET', 'get user by id')
            ->addConfig([
                '/users/{id}' => [
                    'PATCH' => 'update user',
                    'DELETE' => 'delete user',
                ],
            ])
            ->addConfig([
                '/users' => [
                    'GET' => 'get users',
                    'POST' => 'post user to collection',
                ],
            ])
            ->build();

        $this->assertSame([
            Dispatcher::FOUND,
            'get user by id',
            ['id' => '123'],
        ], $dispatcher->dispatch('GET', '/users/123'));

        $this->assertSame([
            Dispatcher::FOUND,
            'delete user',
            ['id' => '124'],
        ], $dispatcher->dispatch('DELETE', '/users/124'));

        $this->assertSame([
            Dispatcher::FOUND,
            'get users',
            [],
        ], $dispatcher->dispatch('GET', '/users'));

        $this->assertSame([
            Dispatcher::METHOD_NOT_ALLOWED,
            ['GET', 'POST'],
        ], $dispatcher->dispatch('PATCH', '/users'));

        $this->assertSame([
            Dispatcher::NOT_FOUND,
        ], $dispatcher->dispatch('GET', '/not-found'));
    }
}
