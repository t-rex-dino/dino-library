<?php

declare(strict_types=1);

namespace Dino\Core;

use Dino\Exceptions\ConfigurationException;

/**
 * Handler for managing configuration settings
 * 
 * Provides methods to set, retrieve, and check configuration values
 * with proper exception handling for missing keys.
 * 
 * @package Dino\Core
 * @author Your Name <your.email@example.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class ConfigHandler
{
    /**
     * Storage for configuration values
     * 
     * @var array<string, mixed>
     */
    protected array $config = [];

    /**
     * Set a configuration value
     * 
     * Stores a configuration value with the specified key.
     * If the key already exists, its value will be overwritten.
     * 
     * @param string $key The configuration key
     * @param mixed $value The value to store
     * 
     * @return void
     * 
     * @example
     * $config->set('database.host', 'localhost');
     * $config->set('app.debug', true);
     */
    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Get a configuration value
     * 
     * Retrieves a configuration value by its key.
     * 
     * @param string $key The configuration key to retrieve
     * 
     * @return mixed The stored configuration value
     * 
     * @throws ConfigurationException If the key does not exist
     * 
     * @example
     * $host = $config->get('database.host');
     * $debug = $config->get('app.debug');
     */
    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->config)) {
            throw new ConfigurationException("Configuration key '{$key}' not found.");
        }

        return $this->config[$key];
    }

    /**
     * Check if a configuration key exists
     * 
     * Verifies whether a configuration key is present in the storage.
     * 
     * @param string $key The configuration key to check
     * 
     * @return bool True if the key exists, false otherwise
     * 
     * @example
     * if ($config->has('database.host')) {
     *     // Key exists
     * }
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }
}