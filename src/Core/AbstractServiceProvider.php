<?php
declare(strict_types=1);

namespace Dino\Core;

use Dino\Contracts\ServiceProviderInterface;
use Dino\Core\Utils\ClosureFactory;
use Dino\Core\Utils\InstanceFactory;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    protected array $provides = [];
    protected bool $deferred = false;

    /**
     * Register services into the container.
     */
    abstract public function register(ServiceContainer $container): void;

    /**
     * Optional boot logic after registration.
     */
    public function boot(ServiceContainer $container): void
    {
        // Optional boot method
    }

    /**
     * List of services provided by this provider.
     */
    public function provides(): array
    {
        return $this->provides;
    }

    /**
     * Check if provider is deferred.
     */
    public function isDeferred(): bool
    {
        return $this->deferred;
    }

    /**
     * Bind a concrete instance as a factory.
     */
    protected function bind(ServiceContainer $container, string $id, object $concrete): void
    {
        $container->addFactory($id, new InstanceFactory($concrete));
    }

    /**
     * Register a singleton service.
     */
    protected function singleton(ServiceContainer $container, string $id, object $concrete): void
    {
        $container->addFactory($id, new InstanceFactory($concrete));
    }

    /**
     * Register a factory using ClosureFactory adapter.
     */
    protected function factory(ServiceContainer $container, string $id, callable $factory): void
    {
        $container->addFactory($id, new ClosureFactory($factory));
    }
}
