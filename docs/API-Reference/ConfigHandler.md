# ConfigHandler Class

The `ConfigHandler` class provides a flexible and hierarchical configuration management system for your PHP applications. It supports dot notation for nested configuration access and includes validation capabilities.

## Class Overview

```php

namespace Dino\Core;

class ConfigHandler implements \Dino\Contracts\ConfigurableInterface
{
    // Public methods
    public function set(string $key, mixed $value): void;
    public function get(string $key, mixed $default = null): mixed;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function getAll(): array;
    public function merge(array $config): void;
    public function loadFromArray(array $config): void;
    public function loadFromFile(string $filePath): void;
    public function validate(string $key, callable $validator): bool;

    // Validation system methods
    public function setValidationRules(array $rules): void;
    public function registerValidator(\Dino\Contracts\Validation\ValidatorInterface $validator): void;
}
```

## Methods

*   **set()** – Sets a configuration value using dot notation. Integrates validation before storing values.
*   **get()** – Retrieves a configuration value with optional default.
*   **has()** – Checks if a configuration key exists.
*   **remove()** – Removes a configuration key.
*   **getAll()** – Returns all configuration data.
*   **merge()** – Merges new configuration data.
*   **loadFromArray()** – Loads configuration from array.
*   **loadFromFile()** – Loads configuration from PHP file.
*   **validate()** – Validates a configuration value using a custom validator.
*   **setValidationRules()** – Defines validation rules for keys.
*   **registerValidator()** – Registers a validator instance.

## Usage Examples

```php

$config = new ConfigHandler();
$config->setValidationRules([
    'app.name'  => ['required', 'type:string'],
    'app.port'  => ['required', 'type:int', 'range'],
    'app.email' => ['required', 'regex']
]);

$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

$config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
$config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);
```

## Best Practices

*   Use descriptive keys with consistent naming
*   Group related configurations using dot notation
*   Validate critical configuration values during bootstrap
*   Register validators early
*   Provide sensible defaults