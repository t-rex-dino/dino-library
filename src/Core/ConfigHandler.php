<?php

declare(strict_types=1);

namespace Dino\Core;

use Dino\Exceptions\ConfigurationException;
use Dino\Exceptions\ValidationException;
use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Core\Validation\ValidatorRegistry;

/**
 * Handler for managing configuration settings
 *
 * Provides methods to set, retrieve, and check configuration values
 * with proper exception handling for missing keys.
 * Now extended with validation support.
 *
 * @package Dino\Core
 * @since 1.0.0
 * @version 1.1.1
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
     * Validation rules for configuration keys
     *
     * @var array<string, array<int, string>>
     */
    private array $validationRules = [];

    /**
     * Registry for validators
     *
     * @var ValidatorRegistry
     */
    private ValidatorRegistry $validatorRegistry;

    public function __construct()
    {
        $this->validatorRegistry = new ValidatorRegistry();
    }

    /**
     * Define validation rules for configuration keys
     *
     * @param array<string, array<int, string>> $rules
     */
    public function setValidationRules(array $rules): void
    {
        $this->validationRules = $rules;
    }

    /**
     * Register a validator instance
     *
     * @param ValidatorInterface $validator
     */
    public function registerValidator(ValidatorInterface $validator): void
    {
        $this->validatorRegistry->register($validator);
    }

    /**
     * Set a configuration value
     *
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $context
     *
     * @return void
     *
     * @throws ValidationException If validation fails
     */
    public function set(string $key, mixed $value, array $context = []): void
    {
        $this->validate($key, $value, $context);
        $this->config[$key] = $value;
    }

    /**
     * Get a configuration value
     *
     * @param string $key
     * @return mixed
     *
     * @throws ConfigurationException If the key does not exist
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
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Validate a configuration value against defined rules
     *
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $context
     *
     * @throws ValidationException
     */
    private function validate(string $key, mixed $value, array $context = []): void
    {
        if (!isset($this->validationRules[$key])) {
            return; // no rules defined for this key
        }

        foreach ($this->validationRules[$key] as $rule) {
            $validator = $this->validatorRegistry->getValidatorForRule($rule);
            if ($validator === null) {
                throw new ValidationException("No validator found for rule '{$rule}'", [
                    'configKey' => $key,
                    'rule' => $rule
                ]);
            }

            $validator->validate($value, array_merge($context, [
                'configKey' => $key,
                'rule' => $rule
            ]));
        }
    }
}
