<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;
use Dino\Exceptions\ServiceResolutionException;
use Dino\Exceptions\ServiceNotFoundException;

final class ServiceContainerTest extends TestCase
{
    public function testAddFactoryAndCreateService(): void
    {
        $container = new ServiceContainer();

        $factory = new class implements FactoryInterface {
            public function create(mixed ...$parameters): object
            {
                return new stdClass();
            }
        };

        $container->addFactory('test', $factory);
        $this->assertInstanceOf(stdClass::class, $container->get('test'));
    }

    public function testHasFactory(): void
    {
        $container = new ServiceContainer();

        $factory = new class implements FactoryInterface {
            public function create(mixed ...$parameters): object
            {
                return new stdClass();
            }
        };

        $container->addFactory('test', $factory);

        $this->assertTrue($container->has('test'));
        $this->assertFalse($container->has('missing'));
    }

    public function testGetMissingFactoryThrowsException(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $container = new ServiceContainer();
        $container->get('unknown');
    }
}
