<?php
declare(strict_types=1);

namespace Dino\Core;

use ReflectionClass;
use Dino\Core\Utils\ClosureFactory;
use Dino\Core\Utils\InstanceFactory;
use Dino\Exceptions\ServiceResolutionException;

class DependencyResolver
{
    private ServiceContainer $container;
    private ParameterResolver $parameterResolver;

    public function __construct(ServiceContainer $container, ?ParameterResolver $parameterResolver = null)
    {
        $this->container = $container;
        $this->parameterResolver = $parameterResolver ?? new ParameterResolver();
    }

    /**
     * Resolve and instantiate a class using constructor injection (auto-wiring).
     * Also caches the created instance in the container for subsequent retrievals.
     */
    public function resolve(string $className): object
    {
        // Return cached instance if already in container
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isInstantiable() === false) {
            throw new ServiceResolutionException(
                $className,
                ['reason' => 'Class is not instantiable']
            );
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            $instance = new $className();
            $this->container->addFactory($className, new InstanceFactory($instance));
            return $instance;
        }

        $parameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            $parameters[] = $this->parameterResolver->resolve($parameter, $this->container);
        }

        $instance = $reflection->newInstanceArgs($parameters);
        $this->container->addFactory($className, new InstanceFactory($instance));

        return $instance;
    }

    /**
     * Register interface-to-implementation binding.
     * Resolves the implementation via DI when the interface is requested.
     */
    public function bindInterface(string $interface, string $implementation): void
    {
        $this->container->addFactory($interface, new ClosureFactory(
            fn() => $this->resolve($implementation)
        ));
    }

    /**
     * Resolve with custom parameters for contextual binding.
     * Provided parameter names map to constructor parameter names.
     */
    public function resolveWith(string $className, array $customParameters = []): object
    {
        // Return cached instance if already in container
        if ($this->container->has($className) && empty($customParameters)) {
            return $this->container->get($className);
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isInstantiable() === false) {
            throw new ServiceResolutionException(
                $className,
                ['reason' => 'Class is not instantiable']
            );
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            $instance = new $className();
            // Only cache when no contextual overrides are provided
            if (empty($customParameters)) {
                $this->container->addFactory($className, new InstanceFactory($instance));
            }
            return $instance;
        }

        $parameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            $paramName = $parameter->getName();

            if (array_key_exists($paramName, $customParameters)) {
                $parameters[] = $customParameters[$paramName];
                continue;
            }

            $parameters[] = $this->parameterResolver->resolve($parameter, $this->container);
        }

        $instance = $reflection->newInstanceArgs($parameters);

        // Cache only when not using contextual overrides
        if (empty($customParameters)) {
            $this->container->addFactory($className, new InstanceFactory($instance));
        }

        return $instance;
    }
}
