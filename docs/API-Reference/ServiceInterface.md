# ServiceInterface

The `ServiceInterface` defines the fundamental contract for all services in the Dino Library. It provides a standardized lifecycle management system for services, ensuring consistent initialization, operation, and cleanup across all service implementations.

## Interface Overview

```php
namespace Dino\Contracts;

interface ServiceInterface
{
    /**
     * Initialize the service
     * 
     * @return void
     * @throws \RuntimeException If initialization fails
     */
    public function initialize(): void;
    
    /**
     * Check if the service is initialized and ready
     * 
     * @return bool True if service is ready, false otherwise
     */
    public function isReady(): bool;
    
    /**
     * Get the service name or identifier
     * 
     * @return string Service identifier
     */
    public function getName(): string;
    
    /**
     * Gracefully shutdown the service
     * 
     * @return void
     */
    public function shutdown(): void;
}
```

## Method Details

### initialize()

Initializes the service, preparing it for operation.

```php
/**
 * Initialize the service
 * 
 * @return void
 * @throws \RuntimeException If initialization fails
 */
public function initialize(): void
```

#### Throws

*   `\RuntimeException` - If service initialization fails

### isReady()

Checks if the service is initialized and ready for use.

```php
/**
 * Check if the service is initialized and ready
 * 
 * @return bool True if service is ready, false otherwise
 */
public function isReady(): bool
```

#### Returns

*   `bool` - True if service is ready, false otherwise

### getName()

Returns the unique identifier for the service.

```php
/**
 * Get the service name or identifier
 * 
 * @return string Service identifier
 */
public function getName(): string
```

#### Returns

*   `string` - Service identifier

### shutdown()

Gracefully shuts down the service, performing any necessary cleanup.

```php
/**
 * Gracefully shutdown the service
 * 
 * @return void
 */
public function shutdown(): void
```

## Implementation Guidelines

### Basic Service Implementation

```php
use Dino\Contracts\ServiceInterface;

class DatabaseService implements ServiceInterface
{
    private bool $initialized = false;
    private string $name = 'database.service';
    private ?\PDO $connection = null;
    
    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        
        try {
            $this->connection = new \PDO(
                'mysql:host=localhost;dbname=myapp',
                'username',
                'password'
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->initialized = true;
            
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database service initialization failed: ' . $e->getMessage());
        }
    }
    
    public function isReady(): bool
    {
        return $this->initialized && $this->connection !== null;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function shutdown(): void
    {
        $this->connection = null;
        $this->initialized = false;
    }
    
    public function getConnection(): \PDO
    {
        if (!$this->isReady()) {
            throw new \RuntimeException('Database service is not ready');
        }
        
        return $this->connection;
    }
}
```

### Configurable Service Implementation

```php
use Dino\Contracts\ServiceInterface;
use Dino\Contracts\ConfigurableInterface;

class CacheService implements ServiceInterface, ConfigurableInterface
{
    private bool $initialized = false;
    private array $config = [];
    private $redis = null;
    
    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        
        if (!$this->validateConfig($this->config)) {
            throw new \RuntimeException('Cache service configuration is invalid');
        }
        
        try {
            $this->redis = new \Redis();
            $connected = $this->redis->connect(
                $this->config['host'] ?? '127.0.0.1',
                $this->config['port'] ?? 6379,
                $this->config['timeout'] ?? 5.0
            );
            
            if (!$connected) {
                throw new \RuntimeException('Failed to connect to Redis');
            }
            
            if (isset($this->config['password'])) {
                $this->redis->auth($this->config['password']);
            }
            
            $this->initialized = true;
            
        } catch (\RedisException $e) {
            throw new \RuntimeException('Cache service initialization failed: ' . $e->getMessage());
        }
    }
    
    public function isReady(): bool
    {
        return $this->initialized && $this->redis !== null;
    }
    
    public function getName(): string
    {
        return 'cache.service';
    }
    
    public function shutdown(): void
    {
        if ($this->redis) {
            $this->redis->close();
            $this->redis = null;
        }
        $this->initialized = false;
    }
    
    // ConfigurableInterface methods
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
    
    public function validateConfig(array $config): bool
    {
        return isset($config['host']) && is_string($config['host']);
    }
    
    // Cache-specific methods
    public function get(string $key)
    {
        if (!$this->isReady()) {
            throw new \RuntimeException('Cache service is not ready');
        }
        
        return $this->redis->get($key);
    }
    
    public function set(string $key, $value, int $ttl = null): bool
    {
        if (!$this->isReady()) {
            throw new \RuntimeException('Cache service is not ready');
        }
        
        return $this->redis->set($key, $value, $ttl);
    }
}
```

## Usage Examples

### Basic Service Management

```php
use Dino\Core\ServiceContainer;

// Create and configure services
$cacheService = new CacheService();
$cacheService->setConfig([
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => 'secret'
]);

$dbService = new DatabaseService();

// Initialize services
try {
    $cacheService->initialize();
    $dbService->initialize();
} catch (\RuntimeException $e) {
    error_log('Service initialization failed: ' . $e->getMessage());
    exit(1);
}

// Check service readiness
if ($cacheService->isReady() && $dbService->isReady()) {
    echo "All services are ready!\n";
    echo "Cache service: " . $cacheService->getName() . "\n";
    echo "Database service: " . $dbService->getName() . "\n";
}

// Use services
// ...

// Graceful shutdown
$cacheService->shutdown();
$dbService->shutdown();
```

### Service Integration with ServiceContainer

```php
use Dino\Core\ServiceContainer;
use Dino\Contracts\ServiceProviderInterface;

class ApplicationServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Register services with their configuration
        $container->register('cache', CacheService::class)
            ->setConfig($container->get('config')->get('cache', []));
            
        $container->register('database', DatabaseService::class);
        $container->register('mailer', MailService::class)
            ->setConfig($container->get('config')->get('mail', []));
    }
}

// Bootstrap application
$container = new ServiceContainer();
$container->addServiceProvider(new ApplicationServiceProvider());

// Initialize all services
$services = ['cache', 'database', 'mailer'];
foreach ($services as $serviceName) {
    if ($container->has($serviceName)) {
        $service = $container->get($serviceName);
        if ($service instanceof ServiceInterface && !$service->isReady()) {
            $service->initialize();
        }
    }
}

// Application shutdown
register_shutdown_function(function() use ($container) {
    foreach ($services as $serviceName) {
        if ($container->has($serviceName)) {
            $service = $container->get($serviceName);
            if ($service instanceof ServiceInterface && $service->isReady()) {
                $service->shutdown();
            }
        }
    }
});
```

### Service Health Monitoring

```php
class ServiceManager
{
    private array $services = [];
    
    public function registerService(ServiceInterface $service): void
    {
        $this->services[$service->getName()] = $service;
    }
    
    public function initializeAll(): void
    {
        foreach ($this->services as $service) {
            try {
                if (!$service->isReady()) {
                    $service->initialize();
                    echo "Initialized: " . $service->getName() . "\n";
                }
            } catch (\RuntimeException $e) {
                error_log("Failed to initialize {$service->getName()}: " . $e->getMessage());
            }
        }
    }
    
    public function getServiceStatus(): array
    {
        $status = [];
        foreach ($this->services as $service) {
            $status[$service->getName()] = [
                'ready' => $service->isReady(),
                'name' => $service->getName()
            ];
        }
        return $status;
    }
    
    public function shutdownAll(): void
    {
        foreach ($this->services as $service) {
            if ($service->isReady()) {
                $service->shutdown();
                echo "Shutdown: " . $service->getName() . "\n";
            }
        }
    }
}

// Usage
$serviceManager = new ServiceManager();
$serviceManager->registerService(new CacheService());
$serviceManager->registerService(new DatabaseService());

// Initialize and monitor
$serviceManager->initializeAll();
$status = $serviceManager->getServiceStatus();

// Graceful shutdown on termination
pcntl_signal(SIGTERM, function() use ($serviceManager) {
    $serviceManager->shutdownAll();
    exit(0);
});
```

## Advanced Service Patterns

### Lazy Initialization Service

```php
class LazyService implements ServiceInterface
{
    private bool $initialized = false;
    private bool $shouldInitialize = false;
    private $resource = null;
    
    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        
        $this->resource = $this->createResource();
        $this->initialized = true;
    }
    
    public function isReady(): bool
    {
        return $this->initialized && $this->resource !== null;
    }
    
    public function getName(): string
    {
        return 'lazy.service';
    }
    
    public function shutdown(): void
    {
        $this->cleanupResource($this->resource);
        $this->resource = null;
        $this->initialized = false;
    }
    
    public function execute(callable $operation)
    {
        if (!$this->isReady()) {
            $this->initialize();
        }
        
        return $operation($this->resource);
    }
    
    private function createResource()
    {
        // Expensive resource creation
        return expensiveOperation();
    }
    
    private function cleanupResource($resource): void
    {
        // Cleanup logic
    }
}
```

### Composite Service

```php
class CompositeService implements ServiceInterface
{
    private array $services = [];
    
    public function addService(ServiceInterface $service): void
    {
        $this->services[] = $service;
    }
    
    public function initialize(): void
    {
        foreach ($this->services as $service) {
            if (!$service->isReady()) {
                $service->initialize();
            }
        }
    }
    
    public function isReady(): bool
    {
        foreach ($this->services as $service) {
            if (!$service->isReady()) {
                return false;
            }
        }
        return true;
    }
    
    public function getName(): string
    {
        return 'composite.service';
    }
    
    public function shutdown(): void
    {
        foreach ($this->services as $service) {
            if ($service->isReady()) {
                $service->shutdown();
            }
        }
    }
}
```

## Testing Service Implementations

```php
class ServiceInterfaceTest extends TestCase
{
    public function testServiceLifecycle(): void
    {
        $service = new CacheService();
        $service->setConfig(['host' => '127.0.0.1']);
        
        $this->assertFalse($service->isReady());
        $this->assertEquals('cache.service', $service->getName());
        
        $service->initialize();
        $this->assertTrue($service->isReady());
        
        $service->shutdown();
        $this->assertFalse($service->isReady());
    }
    
    public function testServiceInitializationFailure(): void
    {
        $this->expectException(\RuntimeException::class);
        
        $service = new CacheService();
        $service->setConfig(['host' => '']); // Invalid config
        $service->initialize();
    }
    
    public function testServiceReadiness(): void
    {
        $service = new DatabaseService();
        
        $this->assertFalse($service->isReady());
        
        $service->initialize();
        $this->assertTrue($service->isReady());
        
        $service->shutdown();
        $this->assertFalse($service->isReady());
    }
}
```

## Best Practices

*   Always check `isReady()` before using service functionality
*   Implement proper error handling in `initialize()` method
*   Ensure `shutdown()` properly cleans up resources
*   Use descriptive and unique service names
*   Implement service health checks for critical services
*   Consider implementing `ConfigurableInterface` for configurable services
*   Use dependency injection for service dependencies
*   Implement proper resource management in long-running services

## Related Components

*   [ConfigurableInterface](ConfigurableInterface.md) - For configurable service implementations
*   [ServiceContainer](ServiceContainer.md) - For managing service instances
*   [ServiceProviderInterface](ServiceProviderInterface.md) - For registering services
*   [FactoryInterface](FactoryInterface.md) - For creating service instances
