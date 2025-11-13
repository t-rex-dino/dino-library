<?php
declare(strict_types=1);

namespace Dino\Core;

use ReflectionParameter;
use Dino\Exceptions\CircularDependencyException;
use Dino\Exceptions\UnresolvableParameterException;

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
                throw new CircularDependencyException(
                    $className,
                    ['reason' => "Circular dependency detected for {$className}"]
                );
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
        throw new UnresolvableParameterException(
            $paramName,
            ['type' => $typeName, 'reason' => 'Parameter could not be resolved']
        );
    }
}
