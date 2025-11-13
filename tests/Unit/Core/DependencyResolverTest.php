<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ServiceContainer;
use Dino\Core\DependencyResolver;
use Dino\Exceptions\CircularDependencyException;

// Example interface and implementation
interface EngineInterface {
    public function start(): string;
}

class Engine implements EngineInterface {
    public function start(): string { return "Engine started"; }
}

// Example class depending on interface
class Car {
    private EngineInterface $engine;
    public function __construct(EngineInterface $engine) { $this->engine = $engine; }
    public function drive(): string { return $this->engine->start() . " - Car is driving"; }
}

// Circular dependency classes
class CircularA {
    public function __construct(CircularB $b) {}
}
class CircularB {
    public function __construct(CircularA $a) {}
}

final class DependencyResolverTest extends TestCase
{
    public function testAutoWiringResolvesDependencies(): void
    {
        $container = new ServiceContainer();
        $resolver = new DependencyResolver($container);

        // Bind EngineInterface to Engine
        $resolver->bindInterface(EngineInterface::class, Engine::class);

        $car = $resolver->resolve(Car::class);

        $this->assertInstanceOf(Car::class, $car);
        $this->assertEquals("Engine started - Car is driving", $car->drive());
    }

    public function testResolverCachesInstancesInContainer(): void
    {
        $container = new ServiceContainer();
        $resolver = new DependencyResolver($container);

        // Bind EngineInterface to Engine
        $resolver->bindInterface(EngineInterface::class, Engine::class);

        $car1 = $resolver->resolve(Car::class);
        $car2 = $container->get(Car::class);

        $this->assertSame($car1, $car2);
    }

    public function testInterfaceBinding(): void
    {
        $container = new ServiceContainer();
        $resolver = new DependencyResolver($container);

        // Bind EngineInterface to Engine
        $resolver->bindInterface(EngineInterface::class, Engine::class);

        $engine = $container->get(EngineInterface::class);
        $this->assertInstanceOf(Engine::class, $engine);
        $this->assertEquals("Engine started", $engine->start());
    }

    public function testCircularDependencyDetection(): void
    {
        $container = new ServiceContainer();
        $resolver = new DependencyResolver($container);

        $this->expectException(CircularDependencyException::class);
        $this->expectExceptionMessage('Circular dependency detected');

        // Attempt to resolve CircularA which depends on CircularB and vice versa
        $resolver->resolve(CircularA::class);
    }
}
