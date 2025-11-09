# LibraryManager Class

The `LibraryManager` class is the central registry for managing services and components in the Dino Library. It provides a simple and consistent way to register, retrieve, and manage dependencies throughout your application.

## Class Overview

```php
namespace Dino\Core;

class LibraryManager
{
    // Public methods
    public function register(string $name, object $service): void;
    public function get(string $name): object;
    public function has(string $name): bool;
    public function unregister(string $name): void;
    public function getAll(): array;
    public function clear(): void;
}
```

## Methods

### register()

Registers a service with the given name.

```php
/**
 * Register a service instance with the library manager
 * 
 * @param string $name The unique service identifier
 * @param object $service The service instance to register
 * @return void
 * @throws \InvalidArgumentException If service name is empty
 * @throws \RuntimeException If service name is already registered
 */
public function register(string $name, object $service): void
```

#### Parameters

*   `$name` (string) - Unique identifier for the service
*   `$service` (object) - The service instance to register

#### Example

```php
use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('logger', new FileLogger());
$manager->register('cache', new RedisCache());
```

### get()

Retrieves a registered service by name.

```php
/**
 * Get a registered service by name
 * 
 * @param string $name The service identifier
 * @return object The registered service instance
 * @throws \Dino\Exceptions\ServiceException If service is not found
 */
public function get(string $name): object
```

#### Parameters

*   `$name` (string) - The service identifier to retrieve

#### Returns

*   `object` - The registered service instance

#### Example

```php
$logger = $manager->get('logger');
$cache = $manager->get('cache');
```

### has()

Checks if a service is registered.

```php
/**
 * Check if a service is registered
 * 
 * @param string $name The service identifier to check
 * @return bool True if service exists, false otherwise
 */
public function has(string $name): bool
```

#### Example

```php
if ($manager->has('logger')) {
    $logger = $manager->get('logger');
    $logger->info('Service is available');
}
```

### unregister()

Removes a service from the registry.

```php
/**
 * Unregister a service from the library manager
 * 
 * @param string $name The service identifier to remove
 * @return void
 */
public function unregister(string $name): void
```

### getAll()

Returns all registered services.

```php
/**
 * Get all registered services
 * 
 * @return array Array of all registered services [name => service]
 */
public function getAll(): array
```

### clear()

Clears all registered services.

```php
/**
 * Clear all registered services
 * 
 * @return void
 */
public function clear(): void
```

## Usage Examples

### Basic Service Registration and Retrieval

```php
use Dino\Core\LibraryManager;

// Initialize the library manager
$manager = new LibraryManager();

// Register services
$manager->register('database', new DatabaseConnection());
$manager->register('mailer', new EmailService());
$manager->register('validator', new ValidationService());

// Use services
$db = $manager->get('database');
$mailer = $manager->get('mailer');
$validator = $manager->get('validator');
```

### Service Availability Check

```php
// Check before using
if ($manager->has('cache')) {
    $cache = $manager->get('cache');
    $data = $cache->get('user_data');
}

// Safe service usage with try-catch
try {
    $service = $manager->get('optional_service');
    $service->execute();
} catch (\Dino\Exceptions\ServiceException $e) {
    // Handle missing service gracefully
    error_log('Optional service not available: ' . $e->getMessage());
}
```

## Best Practices

*   Use descriptive and consistent naming for service identifiers
*   Register all dependencies during application bootstrap
*   Check service availability with `has()` before using optional services
*   Use the same service instance throughout the application for singleton services
*   Clear services during testing to ensure test isolation

## Related Components

*   [ServiceContainer](ServiceContainer.md) - For more advanced dependency injection
*   [ConfigHandler](ConfigHandler.md) - For configuration management
*   [ServiceInterface](ServiceInterface.md) - For implementing service contracts
