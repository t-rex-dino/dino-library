<?php

declare(strict_types=1);

namespace Dino\Core;

use Dino\Exceptions\ConfigNotFoundException;
use Dino\Exceptions\ConfigValidationException;
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
 * @version 1.2.1
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
     * @throws ConfigValidationException If validation fails
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
     * @throws ConfigNotFoundException If the key does not exist
     */
    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->config)) {
            throw new ConfigNotFoundException(
                $key,
                ['reason' => 'Configuration key not found']
            );
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

    private function validate(string $key, mixed $value, array $context = []): void
    {
        if (!isset($this->validationRules[$key])) {
            return;
        }

        $rules = $this->validationRules[$key];
        $errors = [];

        foreach ($rules as $rule) {
            if (!$this->validatorRegistry->supports($rule)) {
                continue;
            }

            try {
                // تجزیه rule به نام و پارامترها
                $ruleName = $rule;
                $ruleParams = [];
                
                if (str_contains($rule, ':')) {
                    list($ruleName, $paramString) = explode(':', $rule, 2);
                    $ruleParams = $this->parseRuleParams($ruleName, $paramString);
                }
                
                // ترکیب context اصلی با پارامترهای rule
                $validationContext = array_merge($context, $ruleParams, ['configKey' => $key]);
                
                $this->validatorRegistry->validate($ruleName, $value, $validationContext);
            } catch (ConfigValidationException $e) {
                $errors[] = $e->getErrorMessage();
            }
        }

        if (!empty($errors)) {
            throw new ConfigValidationException(
                $key,
                array_merge($context, [
                    'errors' => $errors,
                    'rules' => $rules,
                    'value' => $value
                ])
            );
        }
    }
    
    private function parseRuleParams(string $ruleName, string $paramString): array
    {
        switch ($ruleName) {
            case 'type':
                return ['expectedType' => $paramString];
                
            case 'range':
                if (str_contains($paramString, '-')) {
                    list($min, $max) = explode('-', $paramString, 2);
                    return ['min' => (int)$min, 'max' => (int)$max];
                }
                return [];
                
            case 'regex':
                return ['pattern' => $paramString];
                
            default:
                return [];
        }
    }
}