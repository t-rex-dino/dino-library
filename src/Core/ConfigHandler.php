<?php

declare(strict_types=1);

namespace Dino\Core;

use Dino\Exceptions\ConfigurationException;

class ConfigHandler
{
    protected array $config = [];

    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->config)) {
            throw new ConfigurationException("Configuration key '{$key}' not found.");
        }

        return $this->config[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }
}
