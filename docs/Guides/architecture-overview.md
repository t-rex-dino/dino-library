# Architecture Overview

This guide provides a comprehensive overview of the Dino Library architecture, explaining the core components, their relationships, and the design principles that guide the library's development.

## Architectural Principles

### Modular Design

The library follows a modular architecture where each component has a single responsibility and clear boundaries. This enables:

*   Independent development and testing of components
*   Easy replacement of implementations
*   Better code organization and maintainability

### Interface Segregation

Small, focused interfaces define clear contracts between components:

```php
// Instead of one large interface
interface MonolithicService {
    public function configure();
    public function initialize();
    public function execute();
    public function cleanup();
}

// We use segregated interfaces
interface ConfigurableInterface {
    public function setConfig(array $config);
}

interface ServiceInterface {
    public function initialize();
    public function isReady();
    public function shutdown();
}
```

### Dependency Injection

The library promotes constructor injection and interface-based dependencies:

```php
class OrderService implements ServiceInterface {
    public function __construct(
        private PaymentGateway $payment,
        private EmailService $mailer,
        private LoggerInterface $logger
    ) {}
}
```

## Core Components Architecture

### Component Relationships

```text
┌─────────────────┐    ┌──────────────────┐    ┌────────────────────┐
│   Application   │───▶│ ServiceContainer │───▶│ ServiceProvider    │
│                 │    │                  │    │ Interface          │
└─────────────────┘    └──────────────────┘    └────────────────────┘
         │                       │                         │
         │                       │                         │
         ▼                       ▼                         ▼
┌─────────────────┐    ┌──────────────────┐    ┌────────────────────┐
│  LibraryManager │    │   ConfigHandler  │    │ FactoryInterface   │
│                 │    │                  │    │                    │
└─────────────────┘    └──────────────────┘    └────────────────────┘
         │                       │                         │
         │                       │                         │
         ▼                       ▼                         ▼
┌─────────────────┐    ┌──────────────────┐    ┌────────────────────┐
│ ServiceInterface│    │ Configurable     │    │ Concrete Services  │
│                 │    │ Interface        │    │                    │
└─────────────────┘    └──────────────────┘    └────────────────────┘
```

### Data Flow

```text
Configuration Data Flow:
Config Files/Sources → ConfigHandler → Configurable Services

Service Lifecycle Flow:
ServiceProvider → ServiceContainer → ServiceInterface.initialize()

Dependency Resolution:
Service Request → ServiceContainer → FactoryInterface → Service Instance
```

## Component Details

### ServiceContainer - The Heart of DI

The ServiceContainer implements PSR-11 and manages the complete service lifecycle:

```php
class ServiceContainer implements ContainerInterface {
    // Service registration and retrieval
    public function register(string $name, string $class, array $deps = []);
    public function singleton(string $name, string $class, array $deps = []);
    public function get(string $id);
    
    // Factory management
    public function addFactory(string $name, FactoryInterface $factory);
    
    // Provider integration
    public function addServiceProvider(ServiceProviderInterface $provider);
}
```

#### Service Resolution Process

```text
1. Service requested via get('service.name')
2. Check if service exists in instances cache
3. If not, check if factory exists for service
4. If factory exists, use factory->create()
5. If no factory, use reflection to instantiate class
6. Inject dependencies recursively
7. Cache instance if singleton
8. Return service instance
```

### ConfigHandler - Configuration Management

Hierarchical configuration with dot notation support:

```php
$config = new ConfigHandler();
$config->set('database.connections.mysql.host', 'localhost');
$host = $config->get('database.connections.mysql.host');
```

#### Configuration Sources

*   **PHP Arrays:** `loadFromArray()`
*   **PHP Files:** `loadFromFile()`
*   **Environment Variables:** Custom integration
*   **Runtime Configuration:** `set()` method

### LibraryManager - Service Registry

Simple service locator pattern for basic use cases:

```php
$manager = new LibraryManager();
$manager->register('logger', new FileLogger());
$logger = $manager->get('logger');
```

## Design Patterns Implemented

### Factory Pattern

Used for complex object creation with `FactoryInterface`:

```php
class DatabaseFactory implements FactoryInterface {
    public function create(...$params): object {
        [$config] = $params;
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['database']}",
            $config['username'],
            $config['password']
        );
    }
}
```

### Service Provider Pattern

Modular service registration with `ServiceProviderInterface`:

```php
class DatabaseServiceProvider implements ServiceProviderInterface {
    public function register(ServiceContainer $container): void {
        $container->singleton('database', PDOConnection::class, [
            $container->get('config')->get('database.dsn'),
            $container->get('config')->get('database.username'),
            $container->get('config')->get('database.password')
        ]);
    }
}
```

### Observer Pattern

Service lifecycle events through `ServiceInterface`:

```php
class MonitoredService implements ServiceInterface {
    private array $listeners = [];
    
    public function addShutdownListener(callable $listener): void {
        $this->listeners[] = $listener;
    }
    
    public function shutdown(): void {
        foreach ($this->listeners as $listener) {
            $listener($this);
        }
        // Cleanup logic
    }
}
```

## Layer Architecture

### Application Layer

User-facing components and application integration:

```php
// Application bootstrap
$container = new ServiceContainer();
$container->addServiceProvider(new AppServiceProvider());

// Service initialization
$services = ['database', 'cache', 'mailer'];
foreach ($services as $service) {
    $instance = $container->get($service);
    if ($instance instanceof ServiceInterface) {
        $instance->initialize();
    }
}
```

### Service Layer

Business logic and service implementations:

```php
class OrderProcessingService implements ServiceInterface {
    public function __construct(
        private PaymentService $payment,
        private InventoryService $inventory,
        private NotificationService $notifications
    ) {}
    
    public function processOrder(Order $order): void {
        $this->payment->charge($order);
        $this->inventory->reserve($order->getItems());
        $this->notifications->sendConfirmation($order);
    }
}
```

### Infrastructure Layer

Technical concerns and external integrations:

```php
class DatabaseConnection implements ServiceInterface {
    public function initialize(): void {
        $this->connection = new PDO($this->config['dsn'], ...);
    }
}

class CacheService implements ServiceInterface, ConfigurableInterface {
    // Cache implementation
}
```

## Integration Patterns

### Framework Integration

How Dino Library integrates with PHP frameworks:

```php
// Laravel Service Provider example
class DinoServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton('dino.container', function($app) {
            $container = new ServiceContainer();
            $container->addServiceProvider(new CoreServiceProvider());
            return $container;
        });
    }
}

// Symfony Bundle example
class DinoBundle extends Bundle {
    public function build(ContainerBuilder $container): void {
        $container->addCompilerPass(new DinoCompilerPass());
    }
}
```

### Microservices Integration

Using Dino Library in microservices architecture:

```php
class ApiService implements ServiceInterface {
    public function __construct(
        private HttpClient $client,
        private Serializer $serializer
    ) {}
    
    public function initialize(): void {
        $this->client->setBaseUri($this->config['api_endpoint']);
    }
}
```

## Performance Considerations

### Service Caching

Singleton services reduce object creation overhead:

```php
$container->singleton('database', DatabaseConnection::class);
// Same instance returned every time
```

### Lazy Loading

Services are only initialized when first requested:

```php
class LazyService implements ServiceInterface {
    private $initialized = false;
    
    public function isReady(): bool {
        return $this->initialized;
    }
    
    public function initialize(): void {
        if (!$this->initialized) {
            // Expensive initialization
            $this->initialized = true;
        }
    }
}
```

## Security Architecture

### Configuration Security

Secure handling of sensitive configuration data:

```php
class SecureConfigHandler extends ConfigHandler {
    public function get(string $key, mixed $default = null): mixed {
        $value = parent::get($key, $default);
        return $this->decryptIfNeeded($value);
    }
}
```

### Service Isolation

Services operate in isolated contexts with controlled dependencies:

```php
// Each service has explicit dependencies
class PaymentService {
    public function __construct(
        private EncryptionService $encryption,
        private AuditLogger $logger
    ) {}
}
```

## Extension Points

### Custom Service Providers

Extend functionality through custom providers:

```php
class CustomServiceProvider implements ServiceProviderInterface {
    public function register(ServiceContainer $container): void {
        $container->register('custom.service', CustomService::class);
    }
}
```

### Factory Extensions

Customize object creation with specialized factories:

```php
class CustomFactory implements FactoryInterface {
    public function create(...$params): object {
        // Custom creation logic
        return new CustomService(...$params);
    }
}
```

## Best Practices

*   **Use interfaces** for all service contracts
*   **Prefer constructor injection** over setter injection
*   **Register services** through service providers for modularity
*   **Use singletons** for stateless services to improve performance
*   **Implement proper cleanup** in shutdown methods
*   **Validate configuration** before service initialization
*   **Use factories** for complex object creation logic

## Conclusion

The Dino Library architecture is designed to be flexible, maintainable, and performant. By following the principles of modular design, interface segregation, and dependency injection, it provides a solid foundation for building scalable PHP applications.

The clear separation of concerns between configuration management, service container, and business logic services enables developers to build complex applications while maintaining code quality and testability.
