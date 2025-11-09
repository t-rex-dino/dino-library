# FactoryInterface

The `FactoryInterface` defines the contract for service factory implementations. Factories are responsible for creating and configuring service instances, providing a flexible way to control object creation logic within the dependency injection container.

## Interface Overview

```php
namespace Dino\Contracts;

interface FactoryInterface
{
    /**
     * Create a new instance of the service
     * 
     * @param mixed ...$params Constructor parameters
     * @return object The created service instance
     */
    public function create(...$params): object;
}
```

## Method Details

### create()

Creates and returns a new instance of the service.

```php
/**
 * Create a new instance of the service
 * 
 * @param mixed ...$params Variable number of constructor parameters
 * @return object The created service instance
 * @throws \RuntimeException If service creation fails
 */
public function create(...$params): object
```

#### Parameters

*   `...$params` (mixed) - Variable number of parameters passed to the factory

#### Returns

*   `object` - The newly created service instance

#### Throws

*   `\RuntimeException` - If service creation fails

## Implementation Guidelines

### Basic Factory Implementation

```php
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface
{
    public function create(...$params): object
    {
        $logFile = $params[0] ?? '/var/log/app.log';
        $logLevel = $params[1] ?? 'info';
        
        return new FileLogger($logFile, $logLevel);
    }
}
```

### Configuration-Based Factory

```php
class DatabaseConnectionFactory implements FactoryInterface
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function create(...$params): object
    {
        $host = $this->config['host'] ?? 'localhost';
        $database = $this->config['database'] ?? 'app';
        $username = $this->config['username'] ?? 'root';
        $password = $this->config['password'] ?? '';
        
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
        
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
```

## Usage Examples

### Basic Factory Registration and Usage

```php
use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;

// Factory implementation
class CacheFactory implements FactoryInterface
{
    public function create(...$params): object
    {
        $redisHost = $params[0] ?? '127.0.0.1';
        $redisPort = $params[1] ?? 6379;
        
        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);
        
        return new RedisCache($redis);
    }
}

// Register factory with container
$container = new ServiceContainer();
$container->addFactory('cache', new CacheFactory());

// Use the factory-created service
$cache = $container->get('cache');
$cache->set('key', 'value');
```

### Factory with Dependency Injection

```php
class MailerFactory implements FactoryInterface
{
    private $config;
    private $logger;
    
    public function __construct(ConfigHandler $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }
    
    public function create(...$params): object
    {
        $smtpHost = $this->config->get('mail.host', 'localhost');
        $smtpPort = $this->config->get('mail.port', 587);
        $username = $this->config->get('mail.username');
        $password = $this->config->get('mail.password');
        
        $transport = new SmtpTransport($smtpHost, $smtpPort);
        $transport->setUsername($username);
        $transport->setPassword($password);
        
        $mailer = new SmtpMailer($transport);
        $mailer->setLogger($this->logger);
        
        return $mailer;
    }
}

// Usage with dependencies
$mailerFactory = new MailerFactory($config, $logger);
$container->addFactory('mailer', $mailerFactory);
```

### Parameterized Factory Creation

```php
class ServiceFactory implements FactoryInterface
{
    public function create(...$params): object
    {
        // Use parameters for flexible service creation
        $serviceType = $params[0] ?? 'default';
        $config = $params[1] ?? [];
        
        switch ($serviceType) {
            case 'api':
                return new ApiService($config);
            case 'queue':
                return new QueueService($config);
            case 'default':
            default:
                return new DefaultService($config);
        }
    }
}

// Create different services using the same factory
$container->addFactory('service', new ServiceFactory());

// Get API service
$apiService = $container->get('service', 'api', ['timeout' => 30]);

// Get Queue service  
$queueService = $container->get('service', 'queue', ['workers' => 5]);
```

## Advanced Factory Patterns

### Singleton Factory

```php
class SingletonFactory implements FactoryInterface
{
    private $instance = null;
    
    public function create(...$params): object
    {
        if ($this->instance === null) {
            $this->instance = new ExpensiveService(...$params);
        }
        
        return $this->instance;
    }
}
```

### Pooling Factory

```php
class ConnectionPoolFactory implements FactoryInterface
{
    private $pool = [];
    private $maxSize;
    
    public function __construct(int $maxSize = 10)
    {
        $this->maxSize = $maxSize;
    }
    
    public function create(...$params): object
    {
        if (empty($this->pool)) {
            return new DatabaseConnection(...$params);
        }
        
        return array_pop($this->pool);
    }
    
    public function recycle(object $connection): void
    {
        if (count($this->pool) < $this->maxSize) {
            $this->pool[] = $connection;
        }
    }
}
```

### Decorator Factory

```php
class DecoratingFactory implements FactoryInterface
{
    private $innerFactory;
    private $decorators;
    
    public function __construct(FactoryInterface $innerFactory, array $decorators = [])
    {
        $this->innerFactory = $innerFactory;
        $this->decorators = $decorators;
    }
    
    public function create(...$params): object
    {
        $service = $this->innerFactory->create(...$params);
        
        foreach ($this->decorators as $decoratorClass) {
            $service = new $decoratorClass($service);
        }
        
        return $service;
    }
}
```

## Error Handling in Factories

```php
class RobustFactory implements FactoryInterface
{
    public function create(...$params): object
    {
        try {
            // Validate parameters
            if (empty($params[0])) {
                throw new \InvalidArgumentException('Configuration parameter required');
            }
            
            $config = $params[0];
            
            if (!isset($config['api_key'])) {
                throw new \RuntimeException('API key not provided');
            }
            
            return new ApiClient($config);
            
        } catch (\Exception $e) {
            // Log the error
            error_log("Factory creation failed: " . $e->getMessage());
            
            // Return fallback service or rethrow
            throw new \RuntimeException('Service creation failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
```

## Testing Factories

```php
class FactoryTest extends TestCase
{
    public function testFactoryCreatesCorrectInstance(): void
    {
        $factory = new LoggerFactory();
        $logger = $factory->create('/tmp/test.log');
        
        $this->assertInstanceOf(FileLogger::class, $logger);
        $this->assertEquals('/tmp/test.log', $logger->getLogFile());
    }
    
    public function testFactoryWithConfiguration(): void
    {
        $config = ['host' => 'test.db', 'database' => 'test'];
        $factory = new DatabaseConnectionFactory($config);
        $connection = $factory->create();
        
        $this->assertInstanceOf(PDO::class, $connection);
    }
}
```

## Best Practices

*   Keep factories focused on a single responsibility
*   Use dependency injection for factory dependencies
*   Validate parameters before service creation
*   Provide meaningful error messages when creation fails
*   Consider using factories for complex object graphs
*   Implement proper resource cleanup for resource-intensive services
*   Use factories to abstract away complex creation logic

## Related Components

*   [ServiceContainer](ServiceContainer.md) - Uses factories for service creation
*   [ServiceProviderInterface](ServiceProviderInterface.md) - For registering multiple factories
*   [ServiceInterface](ServiceInterface.md) - Common interface for created services
