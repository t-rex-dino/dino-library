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
}
```

## Methods

### set()

Sets a configuration value using dot notation for nested arrays.

```php
/**
 * Set a configuration value
 * 
 * @param string $key Configuration key using dot notation
 * @param mixed $value The value to set
 * @return void
 * @throws \InvalidArgumentException If key is empty or invalid
 */
public function set(string $key, mixed $value): void
```

#### Parameters

*   `$key` (string) - Configuration key in dot notation (e.g., 'database.host')
*   `$value` (mixed) - The value to store

#### Example

```php
use Dino\Core\ConfigHandler;

$config = new ConfigHandler();
$config->set('app.name', 'My Application');
$config->set('database.connections.mysql.host', 'localhost');
$config->set('cache.driver', 'redis');
```

### get()

Retrieves a configuration value with optional default value.

```php
/**
 * Get a configuration value
 * 
 * @param string $key Configuration key using dot notation
 * @param mixed $default Default value if key doesn't exist
 * @return mixed The configuration value or default
 */
public function get(string $key, mixed $default = null): mixed
```

#### Parameters

*   `$key` (string) - Configuration key in dot notation
*   `$default` (mixed) - Default value if key not found (optional)

#### Returns

*   `mixed` - The configuration value or default

#### Example

```php
$appName = $config->get('app.name');
$dbHost = $config->get('database.host', '127.0.0.1');
$debugMode = $config->get('app.debug', false);
```

### has()

Checks if a configuration key exists.

```php
/**
 * Check if a configuration key exists
 * 
 * @param string $key Configuration key to check
 * @return bool True if key exists, false otherwise
 */
public function has(string $key): bool
```

### remove()

Removes a configuration key and its value.

```php
/**
 * Remove a configuration key
 * 
 * @param string $key Configuration key to remove
 * @return void
 */
public function remove(string $key): void
```

### getAll()

Returns all configuration data as an array.

```php
/**
 * Get all configuration data
 * 
 * @return array Complete configuration array
 */
public function getAll(): array
```

### merge()

Merges new configuration data with existing configuration.

```php
/**
 * Merge new configuration with existing data
 * 
 * @param array $config Configuration array to merge
 * @return void
 */
public function merge(array $config): void
```

#### Example

```php
$newConfig = [
    'app' => [
        'version' => '1.0.0',
        'debug' => true
    ]
];
$config->merge($newConfig);
```

### loadFromArray()

Loads configuration from a PHP array, replacing existing configuration.

```php
/**
 * Load configuration from array
 * 
 * @param array $config Configuration array
 * @return void
 */
public function loadFromArray(array $config): void
```

### loadFromFile()

Loads configuration from a PHP file that returns an array.

```php
/**
 * Load configuration from PHP file
 * 
 * @param string $filePath Path to PHP configuration file
 * @return void
 * @throws \Dino\Exceptions\ConfigurationException If file not found or invalid
 */
public function loadFromFile(string $filePath): void
```

#### Example

```php
// config.php
return [
    'app' => [
        'name' => 'My App',
        'env' => 'production'
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'my_database'
    ]
];

// Usage
$config->loadFromFile('config.php');
```

### validate()

Validates a configuration value using a custom validator function.

```php
/**
 * Validate a configuration value
 * 
 * @param string $key Configuration key to validate
 * @param callable $validator Validation function that returns bool
 * @return bool True if validation passes, false otherwise
 */
public function validate(string $key, callable $validator): bool
```

## Usage Examples

### Basic Configuration Setup

```php
use Dino\Core\ConfigHandler;

$config = new ConfigHandler();

// Set basic configuration
$config->set('app.name', 'Dino Library');
$config->set('app.version', '1.0.0');
$config->set('app.debug', true);

// Set nested configuration
$config->set('database.connections.mysql.host', 'localhost');
$config->set('database.connections.mysql.port', 3306);
$config->set('database.connections.mysql.database', 'my_app');

// Retrieve configuration
$appName = $config->get('app.name');
$dbHost = $config->get('database.connections.mysql.host');
$debugMode = $config->get('app.debug');
```

### Loading Configuration from File

```php
// config/app.php
return [
    'name' => 'My Application',
    'version' => '1.0.0',
    'debug' => true,
    'providers' => [
        App\Providers\AppServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ]
];

// Load configuration
$config->loadFromFile('config/app.php');

// Use configuration
if ($config->get('app.debug')) {
    error_reporting(E_ALL);
}
```

### Configuration Validation

```php
// Validate required configuration
$requiredConfigs = ['app.name', 'database.host', 'database.name'];

foreach ($requiredConfigs as $configKey) {
    if (!$config->has($configKey)) {
        throw new Exception("Missing required configuration: $configKey");
    }
}

// Custom validation
$isValidPort = $config->validate('database.port', function($value) {
    return is_int($value) && $value > 0 && $value < 65536;
});

if (!$isValidPort) {
    throw new Exception('Invalid database port configuration');
}
```

## Best Practices

*   Use descriptive keys with consistent naming conventions
*   Group related configurations using dot notation hierarchy
*   Validate critical configuration values during application bootstrap
*   Use configuration files for environment-specific settings
*   Always provide sensible default values for optional configurations
*   Cache configuration data in production environments for better performance

## Related Components

*   [ConfigurableInterface](ConfigurableInterface.md) - Interface for configurable classes
*   [LibraryManager](LibraryManager.md) - For service management
*   [ServiceContainer](ServiceContainer.md) - For dependency injection
