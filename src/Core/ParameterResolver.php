<?php
declare(strict_types=1);

namespace Dino\Core;

use ReflectionParameter;
use RuntimeException;

class ParameterResolver
{
    private array $resolvingTypes = [];

    public function resolve(ReflectionParameter $parameter, ServiceContainer $container): mixed
    {
        $type = $parameter->getType();
        $paramName = $parameter->getName();

        if ($type && !$type->isBuiltin()) {
            $className = $type->getName();

            // Circular dependency guard
            if (isset($this->resolvingTypes[$className])) {
                throw new RuntimeException("Circular dependency detected for: {$className}");
            }

            $this->resolvingTypes[$className] = true;

            try {
                // Resolve by class/interface name if available in container
                if ($container->has($className)) {
                    return $container->get($className);
                }

                // Resolve recursively if not in container
                $resolver = new DependencyResolver($container, $this);
                return $resolver->resolve($className);
            } finally {
                unset($this->resolvingTypes[$className]);
            }
        }

        // Default value if available
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        // Allow null if parameter type permits it
        if ($type && $type->allowsNull()) {
            return null;
        }

        $typeName = $type ? $type->getName() : 'mixed';
        throw new RuntimeException("Cannot resolve parameter: {$paramName} of type: {$typeName}");
    }
}
