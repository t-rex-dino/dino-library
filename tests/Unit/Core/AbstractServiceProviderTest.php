<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ServiceContainer;
use Dino\Core\AbstractServiceProvider;

class DummyService {
    public function hello(): string {
        return "Hello Dino!";
    }
}

class DummyServiceProvider extends AbstractServiceProvider {
    protected array $provides = ['dummy'];

    public function register(ServiceContainer $container): void {
        $this->bind($container, 'dummy', new DummyService());
    }
}

class HeavyService {
    public function compute(): string {
        return "Heavy computation done!";
    }
}

class DeferredServiceProvider extends AbstractServiceProvider {
    protected array $provides = ['heavy.service'];
    protected bool $deferred = true;

    public function register(ServiceContainer $container): void {
        $this->factory($container, 'heavy.service', function() {
            return new HeavyService();
        });
    }
}

final class AbstractServiceProviderTest extends TestCase
{
    public function testProvidesReturnsCorrectServices(): void
    {
        $container = new ServiceContainer();
        $provider = new DummyServiceProvider();
        $provider->register($container);

        $this->assertEquals(['dummy'], $provider->provides());
    }

    public function testRegisterAddsServiceToContainer(): void
    {
        $container = new ServiceContainer();
        $provider = new DummyServiceProvider();
        $provider->register($container);

        $service = $container->get('dummy');
        $this->assertInstanceOf(DummyService::class, $service);
        $this->assertEquals("Hello Dino!", $service->hello());
    }

    public function testDeferredDefaultsToFalse(): void
    {
        $provider = new DummyServiceProvider();
        $this->assertFalse($provider->isDeferred());
    }

    public function testDeferredServiceProviderRegistersFactory(): void
    {
        $container = new ServiceContainer();
        $provider = new DeferredServiceProvider();

        $this->assertTrue($provider->isDeferred());
        $this->assertFalse($container->has('heavy.service'));

        $provider->register($container);
        $this->assertTrue($container->has('heavy.service'));

        $service = $container->get('heavy.service');
        $this->assertInstanceOf(HeavyService::class, $service);
        $this->assertEquals("Heavy computation done!", $service->compute());
    }
}
