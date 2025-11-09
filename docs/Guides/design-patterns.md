# Design Patterns in Dino Library

This guide explores the design patterns implemented in the Dino Library, explaining how each pattern is used and providing practical examples of their implementation.

## Creational Patterns

### Factory Pattern

The Factory Pattern is implemented through the `FactoryInterface` to delegate object creation logic to dedicated factory classes.

#### Implementation

```php
namespace Dino\Contracts;

interface FactoryInterface {
    public function create(...$params): object;
}
```

#### Use Cases

*   Complex object creation with multiple dependencies
*   Object creation that requires configuration
*   Creating different types of objects based on parameters

#### Example: Database Connection Factory

```php
class DatabaseConnectionFactory implements FactoryInterface {
    private array $defaultConfig;
    
    public function __construct(array $defaultConfig = []) {
        $this->defaultConfig = $defaultConfig;
    }
    
    public function create(...$params): object {
        $config = array_merge($this->defaultConfig, $params[0] ?? []);
        
        $dsn = "mysql:host={$config['host']};dbname={$config['database']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        return $pdo;
    }
}

// Usage
$factory = new DatabaseConnectionFactory(['charset' => 'utf8mb4']);
$container->addFactory('database', $factory);
$db = $container->get('database', [['host' => 'localhost', 'database' => 'app']]);
```

#### Example: Multi-format Logger Factory

```php
class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        $type = $params[0] ?? 'file';
        $config = $params[1] ?? [];
        
        switch ($type) {
            case 'file':
                return new FileLogger($config['path'] ?? '/var/log/app.log');
            case 'syslog':
                return new SyslogLogger($config['ident'] ?? 'app');
            case 'null':
                return new NullLogger();
            default:
                throw new InvalidArgumentException("Unknown logger type: $type");
        }
    }
}
```

### Singleton Pattern

The Singleton Pattern is implemented through the ServiceContainer's `singleton()` method to ensure only one instance of a service exists.

#### Implementation

```php
// Register as singleton
$container->singleton('database', DatabaseConnection::class, [$dsn, $user, $pass]);

// Always returns the same instance
$db1 = $container->get('database');
$db2 = $container->get('database');
var_dump($db1 === $db2); // bool(true)
```

#### Use Cases

*   Database connections
*   Configuration handlers
*   Cache services
*   Logging services

### Builder Pattern

While not directly implemented as a separate interface, the Builder Pattern is used in service configuration.

#### Example: Service Configuration Builder

```php
class ServiceBuilder {
    private string $serviceName;
    private string $className;
    private array $dependencies = [];
    private bool $isSingleton = false;
    
    public function __construct(string $serviceName, string $className) {
        $this->serviceName = $serviceName;
        $this->className = $className;
    }
    
    public function withDependencies(array $dependencies): self {
        $this->dependencies = $dependencies;
        return $this;
    }
    
    public function asSingleton(): self {
        $this->isSingleton = true;
        return $this;
    }
    
    public function register(ServiceContainer $container): void {
        if ($this->isSingleton) {
            $container->singleton($this->serviceName, $this->className, $this->dependencies);
        } else {
            $container->register($this->serviceName, $this->className, $this->dependencies);
        }
    }
}

// Usage
$builder = new ServiceBuilder('mailer', SmtpMailer::class);
$builder->withDependencies(['config', 'logger'])
        ->asSingleton()
        ->register($container);
```

## Structural Patterns

### Adapter Pattern

The Adapter Pattern allows incompatible interfaces to work together.

#### Example: PSR-3 Logger Adapter

```php
class PsrLoggerAdapter implements \Psr\Log\LoggerInterface {
    private LoggerInterface $dinoLogger;
    
    public function __construct(LoggerInterface $dinoLogger) {
        $this->dinoLogger = $dinoLogger;
    }
    
    public function emergency($message, array $context = []) {
        $this->dinoLogger->log('emergency', $message, $context);
    }
    
    public function alert($message, array $context = []) {
        $this->dinoLogger->log('alert', $message, $context);
    }
    
    // ... implement other PSR-3 methods
}

// Usage
$dinoLogger = new FileLogger('/var/log/app.log');
$psrLogger = new PsrLoggerAdapter($dinoLogger);
$container->register('logger', $psrLogger);
```

### Decorator Pattern

The Decorator Pattern adds functionality to objects dynamically.

#### Example: Logging Decorator

```php
class LoggingServiceDecorator implements ServiceInterface {
    private ServiceInterface $innerService;
    private LoggerInterface $logger;
    
    public function __construct(ServiceInterface $innerService, LoggerInterface $logger) {
        $this->innerService = $innerService;
        $this->logger = $logger;
    }
    
    public function initialize(): void {
        $this->logger->info("Initializing " . $this->innerService->getName());
        $this->innerService->initialize();
    }
    
    public function isReady(): bool {
        return $this->innerService->isReady();
    }
    
    public function getName(): string {
        return $this->innerService->getName() . '.decorated';
    }
    
    public function shutdown(): void {
        $this->logger->info("Shutting down " . $this->innerService->getName());
        $this->innerService->shutdown();
    }
    
    // Delegate other methods to inner service
    public function __call(string $method, array $args) {
        if (method_exists($this->innerService, $method)) {
            $this->logger->debug("Calling $method on " . $this->innerService->getName());
            return $this->innerService->$method(...$args);
        }
        throw new BadMethodCallException("Method $method not found");
    }
}

// Usage
$database = new DatabaseService();
$loggedDatabase = new LoggingServiceDecorator($database, $logger);
$container->register('database', $loggedDatabase);
```

### Composite Pattern

The Composite Pattern treats individual objects and compositions uniformly.

#### Example: Service Group Composite

```php
class ServiceGroup implements ServiceInterface {
    private array $services = [];
    
    public function addService(ServiceInterface $service): void {
        $this->services[] = $service;
    }
    
    public function initialize(): void {
        foreach ($this->services as $service) {
            if (!$service->isReady()) {
                $service->initialize();
            }
        }
    }
    
    public function isReady(): bool {
        foreach ($this->services as $service) {
            if (!$service->isReady()) {
                return false;
            }
        }
        return true;
    }
    
    public function getName(): string {
        $names = array_map(fn($s) => $s->getName(), $this->services);
        return 'service.group[' . implode(',', $names) . ']';
    }
    
    public function shutdown(): void {
        foreach (array_reverse($this->services) as $service) {
            if ($service->isReady()) {
                $service->shutdown();
            }
        }
    }
}

// Usage
$serviceGroup = new ServiceGroup();
$serviceGroup->addService($database);
$serviceGroup->addService($cache);
$serviceGroup->addService($mailer);

$serviceGroup->initialize(); // Initializes all services
```

## Behavioral Patterns

### Strategy Pattern

The Strategy Pattern defines a family of algorithms and makes them interchangeable.

#### Example: Cache Strategy

```php
interface CacheStrategy {
    public function get(string $key);
    public function set(string $key, $value, int $ttl = null): bool;
    public function delete(string $key): bool;
}

class RedisCacheStrategy implements CacheStrategy, ServiceInterface {
    private \Redis $redis;
    private bool $initialized = false;
    
    public function initialize(): void {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->initialized = true;
    }
    
    public function get(string $key) {
        return $this->redis->get($key);
    }
    
    public function set(string $key, $value, int $ttl = null): bool {
        return $ttl ? $this->redis->setex($key, $ttl, $value) : $this->redis->set($key, $value);
    }
    
    public function delete(string $key): bool {
        return $this->redis->del($key) > 0;
    }
    
    // ... ServiceInterface methods
}

class FileCacheStrategy implements CacheStrategy, ServiceInterface {
    private string $cacheDir;
    
    public function get(string $key) {
        $file = $this->getCacheFile($key);
        return file_exists($file) ? unserialize(file_get_contents($file)) : null;
    }
    
    public function set(string $key, $value, int $ttl = null): bool {
        $file = $this->getCacheFile($key);
        return file_put_contents($file, serialize($value)) !== false;
    }
    
    // ... other methods and ServiceInterface implementation
}

class CacheService implements ServiceInterface {
    private CacheStrategy $strategy;
    
    public function __construct(CacheStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    public function initialize(): void {
        if ($this->strategy instanceof ServiceInterface) {
            $this->strategy->initialize();
        }
    }
    
    // Delegate to strategy
    public function get(string $key) {
        return $this->strategy->get($key);
    }
    
    // ... other ServiceInterface methods
}
```

### Observer Pattern

The Observer Pattern defines a one-to-many dependency between objects.

#### Example: Service Lifecycle Observers

```php
interface ServiceObserver {
    public function onServiceInitialized(ServiceInterface $service): void;
    public function onServiceShutdown(ServiceInterface $service): void;
}

class MonitoringService implements ServiceObserver {
    private array $initializedServices = [];
    
    public function onServiceInitialized(ServiceInterface $service): void {
        $this->initializedServices[$service->getName()] = microtime(true);
        echo "Service initialized: " . $service->getName() . "\n";
    }
    
    public function onServiceShutdown(ServiceInterface $service): void {
        $initTime = $this->initializedServices[$service->getName()] ?? null;
        $uptime = $initTime ? microtime(true) - $initTime : 0;
        echo "Service shutdown: " . $service->getName() . " (uptime: {$uptime}s)\n";
        unset($this->initializedServices[$service->getName()]);
    }
}

class ObservableService implements ServiceInterface {
    private array $observers = [];
    
    public function addObserver(ServiceObserver $observer): void {
        $this->observers[] = $observer;
    }
    
    public function initialize(): void {
        // Initialization logic
        foreach ($this->observers as $observer) {
            $observer->onServiceInitialized($this);
        }
    }
    
    public function shutdown(): void {
        foreach ($this->observers as $observer) {
            $observer->onServiceShutdown($this);
        }
        // Cleanup logic
    }
    
    // ... other ServiceInterface methods
}
```

### Template Method Pattern

The Template Method Pattern defines the skeleton of an algorithm in a method.

#### Example: Abstract Configurable Service

```php
abstract class AbstractConfigurableService implements ServiceInterface, ConfigurableInterface {
    protected array $config = [];
    protected bool $initialized = false;
    
    public function setConfig(array $config): void {
        if (!$this->validateConfig($config)) {
            throw new InvalidArgumentException('Invalid configuration');
        }
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }
    
    public function getConfig(): array {
        return $this->config;
    }
    
    public function initialize(): void {
        if ($this->initialized) {
            return;
        }
        
        $this->preInitialize();
        $this->doInitialize();
        $this->postInitialize();
        
        $this->initialized = true;
    }
    
    public function isReady(): bool {
        return $this->initialized;
    }
    
    public function shutdown(): void {
        $this->preShutdown();
        $this->doShutdown();
        $this->postShutdown();
        
        $this->initialized = false;
    }
    
    protected function preInitialize(): void {
        // Hook for subclasses
    }
    
    protected abstract function doInitialize(): void;
    
    protected function postInitialize(): void {
        // Hook for subclasses
    }
    
    protected function preShutdown(): void {
        // Hook for subclasses
    }
    
    protected abstract function doShutdown(): void;
    
    protected function postShutdown(): void {
        // Hook for subclasses
    }
    
    protected function getDefaultConfig(): array {
        return [];
    }
    
    abstract public function validateConfig(array $config): bool;
    abstract public function getName(): string;
}

class DatabaseService extends AbstractConfigurableService {
    private \PDO $connection;
    
    protected function doInitialize(): void {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";
        $this->connection = new \PDO($dsn, $this->config['username'], $this->config['password']);
    }
    
    protected function doShutdown(): void {
        $this->connection = null;
    }
    
    public function validateConfig(array $config): bool {
        return isset($config['host'], $config['database'], $config['username']);
    }
    
    public function getName(): string {
        return 'database.service';
    }
    
    protected function getDefaultConfig(): array {
        return ['port' => 3306, 'charset' => 'utf8mb4'];
    }
}
```

## Architectural Patterns

### Dependency Injection Pattern

Core to the Dino Library, this pattern is implemented through the ServiceContainer.

#### Constructor Injection

```php
class OrderService implements ServiceInterface {
    public function __construct(
        private PaymentService $payment,
        private InventoryService $inventory,
        private NotificationService $notifications
    ) {}
    
    // ... service methods
}
```

#### Setter Injection

```php
class ConfigurableService implements ConfigurableInterface {
    private ?LoggerInterface $logger = null;
    
    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }
    
    public function setConfig(array $config): void {
        if (isset($config['logger']) && $config['logger'] instanceof LoggerInterface) {
            $this->setLogger($config['logger']);
        }
    }
}
```

### Service Locator Pattern

Implemented through LibraryManager for simple service retrieval.

```php
$manager = new LibraryManager();
$manager->register('logger', new FileLogger());
$logger = $manager->get('logger'); // Service locator pattern
```

## Pattern Combinations

### Factory + Strategy

```php
class CacheStrategyFactory implements FactoryInterface {
    public function create(...$params): object {
        $type = $params[0] ?? 'file';
        
        switch ($type) {
            case 'redis':
                return new RedisCacheStrategy();
            case 'file':
                return new FileCacheStrategy();
            case 'memory':
                return new MemoryCacheStrategy();
            default:
                throw new InvalidArgumentException("Unknown cache strategy: $type");
        }
    }
}

// Usage
$factory = new CacheStrategyFactory();
$redisStrategy = $factory->create('redis');
$cacheService = new CacheService($redisStrategy);
```

### Decorator + Observer

```php
class ObservableDecorator extends LoggingServiceDecorator {
    private array $observers = [];
    
    public function addObserver(ServiceObserver $observer): void {
        $this->observers[] = $observer;
    }
    
    public function initialize(): void {
        parent::initialize();
        foreach ($this->observers as $observer) {
            $observer->onServiceInitialized($this);
        }
    }
}
```

## Best Practices for Pattern Usage

*   **Use Factory Pattern** for complex object creation with dependencies
*   **Prefer Dependency Injection** over Service Locator for testability
*   **Use Decorator Pattern** to add cross-cutting concerns like logging
*   **Implement Strategy Pattern** when you need interchangeable algorithms
*   **Use Singleton Pattern sparingly** - only for truly global state
*   **Combine patterns** to solve complex problems elegantly

## Conclusion

The Dino Library effectively implements several key design patterns that provide flexibility, maintainability, and testability. By understanding these patterns and their implementations, developers can better leverage the library's capabilities and extend it with custom implementations.

Each pattern serves a specific purpose in the architecture, from object creation (Factory, Singleton) to behavior extension (Decorator, Strategy) and structural organization (Composite, Adapter). The combination of these patterns creates a robust foundation for building scalable PHP applications.
