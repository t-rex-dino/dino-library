# ServiceProviderInterface

The `ServiceProviderInterface` defines the contract for service provider implementations. Service providers are used to organize and register related services, factories, and configuration within the dependency injection container, following the modular design pattern.

## Interface Overview

```php
namespace Dino\Contracts;

use Dino\Core\ServiceContainer;

interface ServiceProviderInterface
{
    /**
     * Register services with the container
     * 
     * @param ServiceContainer $container The service container instance
     * @return void
     */
    public function register(ServiceContainer $container): void;
}
```

## Method Details

### register()

Registers services and their dependencies with the service container.

```php
/**
 * Register services with the container
 * 
 * @param ServiceContainer $container The service container instance
 * @return void
 */
public function register(ServiceContainer $container): void
```

#### Parameters

*   `$container` (ServiceContainer) - The service container instance to register services with

#### Returns

*   `void`

## Implementation Guidelines

### Basic Service Provider Implementation

```php
use Dino\Contracts\ServiceProviderInterface;
use Dino\Core\ServiceContainer;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Register database connection as singleton
        $container->singleton('database.connection', PDOConnection::class, [
            'mysql:host=localhost;dbname=myapp',
            'username',
            'password'
        ]);
        
        // Register repository services
        $container->register('user.repository', UserRepository::class, ['database.connection']);
        $container->register('product.repository', ProductRepository::class, ['database.connection']);
    }
}
```

### Configuration-Based Service Provider

```php
class LoggingServiceProvider implements ServiceProviderInterface
{
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
    
    public function register(ServiceContainer $container): void
    {
        $logLevel = $this->config['level'] ?? 'info';
        $logFile = $this->config['file'] ?? '/var/log/app.log';
        
        // Register logger factory
        $container->addFactory('logger', new class($logLevel, $logFile) implements FactoryInterface {
            private $level;
            private $file;
            
            public function __construct(string $level, string $file) {
                $this->level = $level;
                $this->file = $file;
            }
            
            public function create(...$params): object {
                return new FileLogger($this->file, $this->level);
            }
        });
        
        // Register logging services
        $container->register('app.logger', FileLogger::class, [$logFile, $logLevel]);
        $container->register('error.handler', ErrorHandler::class, ['app.logger']);
    }
}
```

## Usage Examples

### Basic Service Provider Registration

```php
use Dino\Core\ServiceContainer;

// Create container
$container = new ServiceContainer();

// Register service providers
$container->addServiceProvider(new DatabaseServiceProvider());
$container->addServiceProvider(new LoggingServiceProvider());
$container->addServiceProvider(new CacheServiceProvider());

// Services are now available
$db = $container->get('database.connection');
$logger = $container->get('app.logger');
$cache = $container->get('cache');
```

### Modular Application Structure

```php
// AppServiceProvider.php - Main application provider
class AppServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Register core services
        $container->singleton('config', ConfigHandler::class);
        $container->singleton('event.dispatcher', EventDispatcher::class);
        
        // Register module providers
        $container->addServiceProvider(new DatabaseServiceProvider());
        $container->addServiceProvider(new AuthServiceProvider());
        $container->addServiceProvider(new ApiServiceProvider());
    }
}

// DatabaseServiceProvider.php - Database module
class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        $container->singleton('database.connection', function() use ($container) {
            $config = $container->get('config');
            return new PDOConnection(
                $config->get('database.dsn'),
                $config->get('database.username'),
                $config->get('database.password')
            );
        });
    }
}

// AuthServiceProvider.php - Authentication module
class AuthServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        $container->register('auth.manager', AuthManager::class, ['database.connection']);
        $container->register('user.provider', UserProvider::class, ['database.connection']);
    }
}
```

### Environment-Specific Service Providers

```php
interface EnvironmentSpecificProvider extends ServiceProviderInterface
{
    public function supports(string $environment): bool;
}

class DevelopmentServiceProvider implements EnvironmentSpecificProvider
{
    public function supports(string $environment): bool
    {
        return $environment === 'development';
    }
    
    public function register(ServiceContainer $container): void
    {
        $container->register('debug.logger', DebugLogger::class);
        $container->register('dev.toolbar', DevelopmentToolbar::class);
    }
}

class ProductionServiceProvider implements EnvironmentSpecificProvider
{
    public function supports(string $environment): bool
    {
        return $environment === 'production';
    }
    
    public function register(ServiceContainer $container): void
    {
        $container->register('performance.monitor', PerformanceMonitor::class);
        $container->register('error.reporter', ErrorReporter::class);
    }
}

// Bootstrap code
$environment = getenv('APP_ENV') ?: 'production';
$providers = [
    new DevelopmentServiceProvider(),
    new ProductionServiceProvider(),
    new CoreServiceProvider(),
];

foreach ($providers as $provider) {
    if (!$provider instanceof EnvironmentSpecificProvider || 
        $provider->supports($environment)) {
        $container->addServiceProvider($provider);
    }
}
```

## Advanced Provider Patterns

### Deferred Service Provider

```php
class DeferredServiceProvider implements ServiceProviderInterface
{
    private $services = [];
    
    public function addService(string $name, string $class, array $dependencies = []): void
    {
        $this->services[$name] = [
            'class' => $class,
            'dependencies' => $dependencies,
            'registered' => false
        ];
    }
    
    public function register(ServiceContainer $container): void
    {
        // Only register services when they are first requested
        foreach ($this->services as $name => &$service) {
            if (!$service['registered']) {
                $container->register($name, $service['class'], $service['dependencies']);
                $service['registered'] = true;
            }
        }
    }
}
```

### Tagged Service Provider

```php
class TaggedServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Register services with tags for organization
        $container->register('user.controller', UserController::class)
                 ->tag('controller')
                 ->tag('api');
                 
        $container->register('product.controller', ProductController::class)
                 ->tag('controller')
                 ->tag('api');
                 
        $container->register('admin.controller', AdminController::class)
                 ->tag('controller')
                 ->tag('admin');
    }
}
```

## Testing Service Providers

```php
class ServiceProviderTest extends TestCase
{
    public function testServiceProviderRegistersServices(): void
    {
        $container = new ServiceContainer();
        $provider = new DatabaseServiceProvider();
        
        $provider->register($container);
        
        $this->assertTrue($container->has('database.connection'));
        $this->assertTrue($container->has('user.repository'));
        $this->assertTrue($container->has('product.repository'));
    }
    
    public function testServiceProviderDependencies(): void
    {
        $container = new ServiceContainer();
        $provider = new AuthServiceProvider();
        
        $provider->register($container);
        
        $authManager = $container->get('auth.manager');
        $this->assertInstanceOf(AuthManager::class, $authManager);
    }
}
```

## Best Practices

*   Group related services in dedicated service providers
*   Use descriptive names for service identifiers
*   Register services in logical dependency order
*   Consider using configuration objects for provider settings
*   Implement environment-specific providers for different deployment scenarios
*   Keep providers focused on a single responsibility or module
*   Use factories for complex service creation logic within providers
*   Test service providers in isolation

## Common Use Cases

*   **Module Registration:** Register all services for a specific application module
*   **Third-party Integration:** Register services for external packages or libraries
*   **Environment Setup:** Configure services differently for development, testing, and production
*   **Feature Flags:** Conditionally register services based on configuration
*   **Plugin Systems:** Allow plugins to register their own services

## Related Components

*   [ServiceContainer](ServiceContainer.md) - The container that uses service providers
*   [FactoryInterface](FactoryInterface.md) - For complex service creation within providers
*   [ConfigurableInterface](ConfigurableInterface.md) - For configurable services registered by providers
*   [ServiceInterface](ServiceInterface.md) - Common interface for services registered by providers
