<?php

declare(strict_types=1);

namespace Dino\Core;

class LibraryManager
{
    protected array $services = [];

    public function register(string $name, object $service): void
    {
        $this->services[$name] = $service;
    }

    public function get(string $name): object
    {
        if (!isset($this->services[$name])) {
            throw new \Dino\Exceptions\ServiceException("Service '{$name}' not found.");
        }

        return $this->services[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }
}
