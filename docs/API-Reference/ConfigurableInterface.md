# ConfigurableInterface

The `ConfigurableInterface` defines the contract for classes that require configuration management. It provides a standardized way to handle configuration data, validation, and lifecycle management for configurable services and components.

## Interface Overview

```php
namespace Dino\Contracts;

interface ConfigurableInterface
{
    /**
     * Set configuration for the object
     * 
     * @param array $config Configuration data
     * @return void
     * @throws \InvalidArgumentException If configuration is invalid
     */
    public function setConfig(array $config): void;
    
    /**
     * Get current configuration
     * 
     * @return array Current configuration data
     */
    public function getConfig(): array;
    
    /**
     * Validate configuration data
     * 
     * @param array $config Configuration data to validate
     * @return bool True if configuration is valid, false otherwise
     */
    public function validateConfig(array $config): bool;
}
```

## Method Details

### setConfig()

Sets the configuration data for the object.

```php
/**
 * Set configuration for the object
 * 
 * @param array $config Configuration data
 * @return void
 * @throws \InvalidArgumentException If configuration is invalid
 */
public function setConfig(array $config): void
```

#### Parameters

*   `$config` (array) - Configuration data as key-value pairs

#### Throws

*   `\InvalidArgumentException` - If the configuration data is invalid

### getConfig()

Retrieves the current configuration data.

```php
/**
 * Get current configuration
 * 
 * @return array Current configuration data
 */
public function getConfig(): array
```

#### Returns

*   `array` - Current configuration data

### validateConfig()

Validates configuration data before applying it.

```php
/**
 * Validate configuration data
 * 
 * @param array $config Configuration data to validate
 * @return bool True if configuration is valid, false otherwise
 */
public function validateConfig(array $config): bool
```

#### Parameters

*   `$config` (array) - Configuration data to validate

#### Returns

*   `bool` - True if configuration is valid, false otherwise

## Implementation Guidelines

### Basic Configurable Class Implementation

```php
use Dino\Contracts\ConfigurableInterface;

class DatabaseConnection implements ConfigurableInterface
{
    private array $config = [];
    
    public function setConfig(array $config): void
    {
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Invalid database configuration');
        }
        
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        $required = ['host', 'database', 'username'];
        
        foreach ($required as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                return false;
            }
        }
        
        return true;
    }
    
    private function getDefaultConfig(): array
    {
        return [
            'port' => 3306,
            'charset' => 'utf8mb4',
            'timeout' => 30
        ];
    }
}
```

### Configuration with Default Values

```php
class CacheService implements ConfigurableInterface
{
    private array $config;
    
    public function __construct(array $initialConfig = [])
    {
        $this->config = $this->getDefaultConfig();
        $this->setConfig($initialConfig);
    }
    
    public function setConfig(array $config): void
    {
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Invalid cache configuration');
        }
        
        $this->config = array_merge($this->config, $config);
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        // Validate TTL
        if (isset($config['ttl']) && (!is_int($config['ttl']) || $config['ttl'] < 0)) {
            return false;
        }
        
        // Validate prefix
        if (isset($config['prefix']) && !is_string($config['prefix'])) {
            return false;
        }
        
        return true;
    }
    
    private function getDefaultConfig(): array
    {
        return [
            'ttl' => 3600,
            'prefix' => 'app_',
            'serializer' => 'php'
        ];
    }
}
```

## Usage Examples

### Basic Configuration Usage

```php
use Dino\Core\ConfigHandler;

// Create configurable service
$database = new DatabaseConnection();

// Configure with valid data
$database->setConfig([
    'host' => 'localhost',
    'database' => 'myapp',
    'username' => 'admin',
    'password' => 'secret',
    'port' => 3307
]);

// Get current configuration
$config = $database->getConfig();
echo "Database host: " . $config['host'];

// Validate configuration before applying
$newConfig = ['host' => '', 'database' => 'myapp']; // Invalid
if ($database->validateConfig($newConfig)) {
    $database->setConfig($newConfig);
} else {
    echo "Invalid configuration provided";
}
```

### Integration with ConfigHandler

```php
use Dino\Core\ConfigHandler;

class ConfigurableService implements ConfigurableInterface
{
    private array $config = [];
    
    public function setConfig(array $config): void
    {
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Invalid service configuration');
        }
        
        $this->config = $config;
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        return isset($config['api_key']) && is_string($config['api_key']);
    }
    
    public function initializeFromConfigHandler(ConfigHandler $configHandler, string $prefix): void
    {
        $serviceConfig = $configHandler->get($prefix, []);
        $this->setConfig($serviceConfig);
    }
}

// Usage
$configHandler = new ConfigHandler();
$configHandler->set('services.api', [
    'api_key' => 'abc123',
    'timeout' => 30,
    'retries' => 3
]);

$apiService = new ConfigurableService();
$apiService->initializeFromConfigHandler($configHandler, 'services.api');
```

### Configuration Inheritance

```php
abstract class AbstractConfigurable implements ConfigurableInterface
{
    protected array $config = [];
    
    public function setConfig(array $config): void
    {
        $validatedConfig = $this->validateAndMerge($config);
        $this->config = $validatedConfig;
        $this->onConfigChanged();
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    abstract public function validateConfig(array $config): bool;
    
    protected function validateAndMerge(array $config): array
    {
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Invalid configuration');
        }
        
        return array_merge($this->getDefaultConfig(), $config);
    }
    
    protected function getDefaultConfig(): array
    {
        return [];
    }
    
    protected function onConfigChanged(): void
    {
        // Hook for subclasses to react to configuration changes
    }
}

class MessagingService extends AbstractConfigurable
{
    public function validateConfig(array $config): bool
    {
        $required = ['provider', 'api_key'];
        
        foreach ($required as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                return false;
            }
        }
        
        return true;
    }
    
    protected function getDefaultConfig(): array
    {
        return [
            'timeout' => 60,
            'retry_attempts' => 3,
            'queue' => 'default'
        ];
    }
    
    protected function onConfigChanged(): void
    {
        // Reinitialize service with new configuration
        $this->initializeService();
    }
    
    private function initializeService(): void
    {
        // Initialize service based on current configuration
    }
}
```

## Advanced Configuration Patterns

### Configuration with Validation Rules

```php
class ValidatedConfigurable implements ConfigurableInterface
{
    private array $config = [];
    private array $validationRules;
    
    public function __construct(array $validationRules = [])
    {
        $this->validationRules = $validationRules;
    }
    
    public function setConfig(array $config): void
    {
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Configuration validation failed');
        }
        
        $this->config = $config;
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        foreach ($this->validationRules as $key => $rule) {
            if (!isset($config[$key])) {
                if ($rule['required'] ?? false) {
                    return false;
                }
                continue;
            }
            
            $value = $config[$key];
            $type = $rule['type'] ?? 'string';
            
            if (!$this->validateType($value, $type)) {
                return false;
            }
            
            if (isset($rule['min']) && $value < $rule['min']) {
                return false;
            }
            
            if (isset($rule['max']) && $value > $rule['max']) {
                return false;
            }
        }
        
        return true;
    }
    
    private function validateType($value, string $type): bool
    {
        switch ($type) {
            case 'string': return is_string($value);
            case 'int': return is_int($value);
            case 'bool': return is_bool($value);
            case 'array': return is_array($value);
            default: return true;
        }
    }
}

// Usage with validation rules
$service = new ValidatedConfigurable([
    'timeout' => ['type' => 'int', 'min' => 1, 'max' => 300],
    'api_key' => ['type' => 'string', 'required' => true],
    'debug' => ['type' => 'bool']
]);
```

### Immutable Configuration

```php
class ImmutableConfigurable implements ConfigurableInterface
{
    private array $config;
    private bool $configured = false;
    
    public function setConfig(array $config): void
    {
        if ($this->configured) {
            throw new \RuntimeException('Configuration cannot be changed after initial setup');
        }
        
        if (!$this->validateConfig($config)) {
            throw new \InvalidArgumentException('Invalid configuration');
        }
        
        $this->config = $config;
        $this->configured = true;
    }
    
    public function getConfig(): array
    {
        if (!$this->configured) {
            throw new \RuntimeException('Service not configured');
        }
        
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        return isset($config['required_setting']);
    }
}
```

## Testing Configurable Classes

```php
class ConfigurableTest extends TestCase
{
    public function testSetAndGetConfig(): void
    {
        $service = new DatabaseConnection();
        $config = ['host' => 'localhost', 'database' => 'test', 'username' => 'user'];
        
        $service->setConfig($config);
        
        $this->assertEquals($config, $service->getConfig());
    }
    
    public function testConfigValidation(): void
    {
        $service = new DatabaseConnection();
        
        $this->assertTrue($service->validateConfig([
            'host' => 'localhost',
            'database' => 'test',
            'username' => 'user'
        ]));
        
        $this->assertFalse($service->validateConfig([
            'host' => '', // Empty host should fail
            'database' => 'test',
            'username' => 'user'
        ]));
    }
    
    public function testInvalidConfigThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $service = new DatabaseConnection();
        $service->setConfig(['invalid' => 'config']);
    }
}
```

## Best Practices

*   Always validate configuration before applying it
*   Provide sensible default values for optional configuration
*   Use clear and descriptive configuration key names
*   Document configuration options and their expected types
*   Consider configuration immutability for services that shouldn't be reconfigured at runtime
*   Use configuration objects for complex configuration structures
*   Implement configuration change listeners for dynamic reconfiguration
*   Test configuration validation thoroughly

## Related Components

*   [ConfigHandler](ConfigHandler.md) - For managing application-wide configuration
*   [ServiceInterface](ServiceInterface.md) - Common interface for services that may be configurable
*   [ServiceContainer](ServiceContainer.md) - For managing configurable service instances
