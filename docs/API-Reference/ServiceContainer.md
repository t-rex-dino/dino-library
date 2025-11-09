# ServiceContainer Class

The `ServiceContainer` class is a powerful dependency injection container that manages service creation, lifecycle, and dependencies. It implements the PSR-11 Container Interface and provides advanced features like factory-based service creation and dependency resolution.

## Class Overview

```php
namespace Dino\Core;

class ServiceContainer implements \Psr\Container\ContainerInterface
{
    // Public methods
    public function get(string $id): object;
    public function has(string $id): bool;
    public function addFactory(string $serviceName, FactoryInterface $factory): void;
    public function addServiceProvider(ServiceProviderInterface $provider): void;
    public function register(string $serviceName, string $className, array $dependencies = []): void;
    public function singleton(string $serviceName, string $className, array $dependencies = []): void;
    public function getRegisteredServices(): array;
    public function clear(): void;
}
```

## Methods

### get()

Retrieves a service from the container by its identifier.

```php
/**
 * Get a service from the container
 * 
 * @param string $id Service identifier
 * @return object The requested service instance
 * @throws \Dino\Exceptions\ContainerException If service cannot be created
 * @throws \Dino\Exceptions\ServiceException If service is not found
 */
public function get(string $id): object
```

#### Parameters

*   `$id` (string) - The service identifier

#### Returns

*   `object` - The service instance

#### Example

```php
use Dino\Core\ServiceContainer;

$container = new ServiceContainer();
$logger = $container->get('logger');
$database = $container->get('database');
```

### has()

Checks if the container can provide a service for the given identifier.

```php
/**
 * Check if the container has a service
 * 
 * @param string $id Service identifier
 * @return bool True if service exists, false otherwise
 */
public function has(string $id): bool
```

### addFactory()

Registers a factory for creating a specific service.

```php
/**
 * Register a service factory
 * 
 * @param string $serviceName Service identifier
 * @param FactoryInterface $factory Factory instance
 * @return void
 */
public function addFactory(string $serviceName, FactoryInterface $factory): void
```

#### Example

```php
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new FileLogger('/var/log/app.log');
    }
}

$container->addFactory('logger', new LoggerFactory());
```

### addServiceProvider()

Registers a service provider to configure multiple services.

```php
/**
 * Register a service provider
 * 
 * @param ServiceProviderInterface $provider Service provider instance
 * @return void
 */
public function addServiceProvider(ServiceProviderInterface $provider): void
```

#### Example

```php
use Dino\Contracts\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface {
    public function register(ServiceContainer $container): void {
        $container->register('database', MySQLDatabase::class, ['host', 'user', 'pass']);
    }
}

$container->addServiceProvider(new DatabaseServiceProvider());
```

### register()

Registers a service with automatic dependency resolution.

```php
/**
 * Register a service with dependency resolution
 * 
 * @param string $serviceName Service identifier
 * @param string $className Fully qualified class name
 * @param array $dependencies Dependency names or values
 * @return void
 */
public function register(string $serviceName, string $className, array $dependencies = []): void
```

#### Example

```php
// Register with dependency names
$container->register('mailer', SmtpMailer::class, ['config', 'logger']);

// Register with concrete values
$container->register('cache', RedisCache::class, ['redis://localhost:6379']);
```

### singleton()

Registers a service as a singleton (shared instance).

```php
/**
 * Register a singleton service
 * 
 * @param string $serviceName Service identifier
 * @param string $className Fully qualified class name
 * @param array $dependencies Dependency names or values
 * @return void
 */
public function singleton(string $serviceName, string $className, array $dependencies = []): void
```

#### Example

```php
$container->singleton('database', DatabaseConnection::class, ['db_config']);
$container->singleton('logger', FileLogger::class, ['/var/log/app.log']);

// Same instance returned every time
$db1 = $container->get('database');
$db2 = $container->get('database');
var_dump($db1 === $db2); // bool(true)
```

### getRegisteredServices()

Returns all registered service identifiers.

```php
/**
 * Get all registered service names
 * 
 * @return array Array of service identifiers
 */
public function getRegisteredServices(): array
```

### clear()

Clears all registered services and factories from the container.

```php
/**
 * Clear all services and factories
 * 
 * @return void
 */
public function clear(): void
```

## Usage Examples

### Basic Service Registration and Retrieval

```php
use Dino\Core\ServiceContainer;

$container = new ServiceContainer();

// Register services
$container->register('validator', ValidatorService::class);
$container->singleton('cache', RedisCache::class, ['redis://127.0.0.1:6379']);
$container->register('mailer', EmailService::class, ['config', 'logger']);

// Use services
$validator = $container->get('validator');
$cache = $container->get('cache');
$mailer = $container->get('mailer');
```

### Using Service Providers

```php
use Dino\Contracts\ServiceProviderInterface;
use Dino\Core\ServiceContainer;

class AppServiceProvider implements ServiceProviderInterface {
    public function register(ServiceContainer $container): void {
        $container->singleton('database', PDOConnection::class, [
            'mysql:host=localhost;dbname=myapp',
            'username',
            'password'
        ]);
        
        $container->register('user.repository', UserRepository::class, ['database']);
        $container->register('auth.service', AuthService::class, ['user.repository']);
    }
}

// Register the provider
$container->addServiceProvider(new AppServiceProvider());

// Services are now available
$auth = $container->get('auth.service');
$users = $container->get('user.repository');
```

### Factory-Based Service Creation

```php
use Dino\Contracts\FactoryInterface;

class ConfigurableServiceFactory implements FactoryInterface {
    private $config;
    
    public function __construct(array $config) {
        $this->config = $config;
    }
    
    public function create(...$params): object {
        return new ConfigurableService($this->config, ...$params);
    }
}

// Register factory with configuration
$factory = new ConfigurableServiceFactory(['timeout' => 30, 'retries' => 3]);
$container->addFactory('api.client', $factory);

$apiClient = $container->get('api.client');
```

### Dependency Resolution

```php
// Services with dependencies
class OrderService {
    public function __construct(
        private PaymentGateway $payment,
        private EmailService $mailer,
        private LoggerInterface $logger
    ) {}
}

class PaymentGateway {
    public function __construct(private Config $config) {}
}

// Registration
$container->register('config', AppConfig::class);
$container->register('payment', PaymentGateway::class, ['config']);
$container->register('logger', FileLogger::class);
$container->register('mailer', SmtpMailer::class);
$container->register('order.service', OrderService::class, ['payment', 'mailer', 'logger']);

// Automatic dependency resolution
$orderService = $container->get('order.service');
// Dependencies are automatically injected
```

## Best Practices

*   Use service providers to organize related service registrations
*   Register stateless services as singletons for better performance
*   Use factories for complex service creation logic
*   Always check service availability with `has()` before retrieval
*   Clear the container between tests to ensure isolation
*   Use descriptive service names that reflect their purpose
*   Register services in dependency order (dependencies first)

## Error Handling

```php
try {
    $service = $container->get('non_existent_service');
} catch (\Dino\Exceptions\ServiceException $e) {
    // Handle service not found
    error_log('Service not available: ' . $e->getMessage());
} catch (\Dino\Exceptions\ContainerException $e) {
    // Handle container errors
    error_log('Container error: ' . $e->getMessage());
}

// Safe service access
if ($container->has('optional_service')) {
    $service = $container->get('optional_service');
    $service->execute();
}
```

## Related Components

*   [FactoryInterface](FactoryInterface.md) - Interface for service factories
*   [ServiceProviderInterface](ServiceProviderInterface.md) - Interface for service providers
*   [ServiceInterface](ServiceInterface.md) - Base interface for services
*   [LibraryManager](LibraryManager.md) - For simple service registration
