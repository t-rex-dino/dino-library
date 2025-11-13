<?php

declare(strict_types=1);

namespace Dino\Core;

use Dino\Contracts\FactoryInterface;
use Dino\Exceptions\ServiceNotFoundException;

class ServiceContainer
{
    protected array $factories = [];

    public function addFactory(string $name, FactoryInterface $factory): void
    {
        $this->factories[$name] = $factory;
    }

    public function get(string $name, mixed ...$parameters): object
    {
        if (!isset($this->factories[$name])) {
            throw new ServiceNotFoundException(
                $name,
                ['reason' => "Factory '{$name}' not found in ServiceContainer"]
            );
        }

        return $this->factories[$name]->create(...$parameters);
    }

    public function has(string $name): bool
    {
        return isset($this->factories[$name]);
    }
}
