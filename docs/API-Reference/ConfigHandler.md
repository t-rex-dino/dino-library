ConfigHandler API Reference - Dino Library v1.2.1 

# ConfigHandler Class

The `ConfigHandler` class provides a flexible and hierarchical configuration management system for your PHP applications. It supports dot notation for nested configuration access and includes advanced validation capabilities with context-aware error reporting.

## Class Overview

```php

namespace Dino\Core;

class ConfigHandler implements \Dino\Contracts\ConfigurableInterface
{
    // Constructor
    public function __construct();
    
    // Configuration Management
    public function set(string $key, mixed $value, array $context = []): void;
    public function get(string $key): mixed;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function getAll(): array;
    public function merge(array $config): void;
    public function loadFromArray(array $config): void;
    public function loadFromFile(string $filePath): void;
    
    // Validation System (Enhanced in v1.2.1)
    public function setValidationRules(array $rules): void;
    public function registerValidator(\Dino\Contracts\Validation\ValidatorInterface $validator): void;
    
    // Utility Methods
    public function clear(): void;
}
    
```

## Methods

### set(string $key, mixed $value, array $context = \[\]): void

Sets a configuration value using dot notation. Integrates validation before storing values with enhanced context-aware error reporting.

#### Parameters

*   `key` (string) - Configuration key using dot notation
*   `value` (mixed) - Configuration value to store
*   `context` (array) - Optional context for validation parameters and error reporting

#### Exceptions

*   `ConfigValidationException` - If validation fails for the value

#### Examples

```php

// Basic usage
$config->set('app.name', 'My Application');

// With validation context parameters
$config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
$config->set('app.email', 'user@example.com', [
    'pattern' => '/^[^@]+@[^@]+\.[^@]+$/'
]);

// Nested configuration
$config->set('database.connections.mysql.host', 'localhost');
    
```

### get(string $key): mixed

Retrieves a configuration value. **Note:** Default parameter removed in v1.2.1 for consistency.

#### Parameters

*   `key` (string) - Configuration key using dot notation

#### Returns

mixed - The configuration value

#### Exceptions

*   `ConfigNotFoundException` - If the key does not exist

### has(string $key): bool

Checks if a configuration key exists.

### remove(string $key): void

Removes a configuration key.

### getAll(): array

Returns all configuration data as an array.

### merge(array $config): void

Merges new configuration data into existing configuration.

### loadFromArray(array $config): void

Loads configuration from an associative array.

### loadFromFile(string $filePath): void

Loads configuration from a PHP file that returns an array.

## Validation System Methods (Enhanced in v1.2.1)

### setValidationRules(array $rules): void

Defines validation rules for configuration keys with intelligent rule parsing support.

#### Parameters

*   `rules` (array) - Validation rules array with rule parsing support

#### Rule Parsing Intelligence

The system automatically parses rule parameters:

*   `type:int` → validates integer type
*   `range:1-100` → validates numeric range 1-100
*   `regex:/pattern/` → validates against regex pattern

#### Examples

```php

$config->setValidationRules([
    'app.name' => ['required'],
    'app.port' => ['type:int', 'range:1-65535'], // Rule parsing
    'app.debug' => ['type:bool'],
    'app.email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
]);
    
```

### registerValidator(ValidatorInterface $validator): void

Registers a validator instance for configuration validation.

#### Examples

```php

use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;

$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());
    
```

## Utility Methods

### clear(): void

Clears all configuration data and validation rules. **New in v1.2.1**

## Error Handling (Enhanced in v1.2.1)

### Context-Aware Exceptions

```php

use Dino\Exceptions\ConfigNotFoundException;
use Dino\Exceptions\ConfigValidationException;

try {
    $config->set('app.port', 'invalid_string');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    echo "Error Code: " . $e->getErrorCode() . "\n";
    echo "Config Key: " . $context['configKey'] . "\n";
    echo "Failed Rules: " . implode(', ', $context['rules']) . "\n";
    echo "Validation Errors: " . implode('; ', $context['errors']) . "\n";
}

try {
    $value = $config->get('non.existent.key');
} catch (ConfigNotFoundException $e) {
    echo "Configuration key not found: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Available keys: " . implode(', ', $context['available_keys'] ?? []) . "\n";
}
    
```

## Complete Usage Example

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;
use Dino\Exceptions\ConfigValidationException;

$config = new ConfigHandler();

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

// Set validation rules with rule parsing
$config->setValidationRules([
    'app.name' => ['required'],
    'app.port' => ['type:int', 'range:1-65535'],
    'app.debug' => ['type:bool'],
    'app.email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
]);

try {
    // These will pass validation
    $config->set('app.name', 'My App');
    $config->set('app.port', 8080);
    $config->set('app.debug', true);
    $config->set('app.email', 'test@example.com');
    
    // Retrieve values
    $appName = $config->get('app.name');
    $appPort = $config->get('app.port');
    
} catch (ConfigValidationException $e) {
    // Handle validation error with rich context
    $context = $e->getContext();
    error_log("Config validation failed: " . $e->getErrorMessage());
    error_log("Context: " . print_r($context, true));
}
    
```

## Best Practices

*   Use descriptive keys with consistent naming conventions
*   Group related configurations using dot notation hierarchy
*   Validate critical configuration values during application bootstrap
*   Register validators early in the application lifecycle
*   Use rule parsing for complex validation scenarios
*   Leverage context-aware error reporting for better debugging
*   Provide sensible defaults and graceful error recovery

## See Also

*   [ValidatorInterface API Reference](ValidatorInterface.html)
*   [ConfigValidationException API Reference](ConfigValidationException.html)
*   [Configuration Management Guide](../Guides/config-management.html)
*   [Validation System Guide](../Guides/validation-system.html)