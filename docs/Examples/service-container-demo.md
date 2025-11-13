Service Container Demo - Dino Library v1.2.1 

# Service Container Advanced Demo

This demo showcases the advanced features of Dino Library Service Container with enhanced error handling and dependency resolution.

## Basic Service Registration

```php

use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;
use Dino\Exceptions\ServiceNotFoundException;
use Dino\Exceptions\ServiceResolutionException;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new Logger($params[0] ?? 'app.log');
    }
}

$container = new ServiceContainer();
$container->addFactory('logger', new LoggerFactory());

try {
    $logger = $container->get('logger');
} catch (ServiceNotFoundException $e) {
    echo "Service Error: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Available services: " . implode(', ', $context['available_services'] ?? []) . "\n";
}
    
```

## Advanced Dependency Injection

```php

use Dino\Core\DependencyResolver;
use Dino\Exceptions\CircularDependencyException;
use Dino\Exceptions\UnresolvableParameterException;

class MailService {
    public function __construct(
        private Logger $logger,
        private ConfigHandler $config
    ) {}
}

class NotificationService {
    public function __construct(
        private MailService $mailer,
        private Logger $logger
    ) {}
}

$resolver = new DependencyResolver($container);

try {
    $mailService = $resolver->resolve(MailService::class);
    $notificationService = $resolver->resolve(NotificationService::class);
} catch (CircularDependencyException $e) {
    echo "Circular Dependency: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Dependency Chain: " . implode(' → ', $context['dependency_chain']) . "\n";
} catch (UnresolvableParameterException $e) {
    echo "Parameter Resolution Failed: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Parameter: " . $context['parameter'] . "\n";
    echo "Type: " . $context['type'] . "\n";
}
    
```

## Lazy Loading Services

```php

use Dino\Core\LazyServiceWrapper;
use Dino\Exceptions\LazyLoadingException;

class HeavyService {
    public function __construct() {
        // Expensive initialization
        sleep(2);
    }
    
    public function process(): string {
        return "Heavy processing completed";
    }
}

// Register as lazy service
$container->singleton('heavyService', function() {
    return new HeavyService();
}, true); // true enables lazy loading

try {
    // Service not initialized yet
    $lazyService = $container->get('heavyService');
    
    // Initialized on first method call
    $result = $lazyService->process();
    echo $result; // "Heavy processing completed"
} catch (LazyLoadingException $e) {
    echo "Lazy Loading Failed: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Service: " . $context['service_id'] . "\n";
    echo "Error: " . $context['error'] . "\n";
}
    
```

## Service Tagging and Grouping

```php

use Dino\Core\ServiceTagRegistry;
use Dino\Core\ServiceGroup;

$tagRegistry = new ServiceTagRegistry();

// Register services with tags
$tagRegistry->register('logger', ['channel' => 'app']);
$tagRegistry->register('mailer', ['channel' => 'notification']);
$tagRegistry->register('cache', ['channel' => 'performance']);

// Find services by tag
$appServices = $tagRegistry->findByTag('channel', 'app');
$notificationServices = $tagRegistry->findByTag('channel', 'notification');

// Service groups
$appGroup = new ServiceGroup('application');
$appGroup->addService('logger', $logger, ['priority' => 1]);
$appGroup->addService('mailer', $mailService, ['priority' => 2]);

echo "Group: " . $appGroup->getName() . "\n";
echo "Services: " . implode(', ', $appGroup->getServiceIds()) . "\n";
    
```

## Service Providers

```php

use Dino\Core\AbstractServiceProvider;
use Dino\Contracts\ServiceProviderInterface;

class AppServiceProvider extends AbstractServiceProvider {
    public function register(): void {
        $this->container->addFactory('logger', new LoggerFactory());
        $this->container->addFactory('mailer', new MailServiceFactory());
        $this->container->singleton('cache', fn() => new CacheService(), true);
    }
    
    public function provides(): array {
        return ['logger', 'mailer', 'cache'];
    }
}

$provider = new AppServiceProvider();
$provider->registerTo($container);

// Verify all services are available
foreach ($provider->provides() as $service) {
    if ($container->has($service)) {
        echo "Service registered: $service\n";
    }
}
    
```

## Error Handling Patterns

```php

use Dino\Core\ErrorMessageFormatter;

function getServiceSafe(ServiceContainer $container, string $serviceId, mixed $default = null) {
    try {
        return $container->get($serviceId);
    } catch (ServiceNotFoundException $e) {
        // Log detailed error information
        $errorDetails = ErrorMessageFormatter::createDetails($e);
        error_log("Service not found: " . json_encode($errorDetails));
        
        return $default;
    } catch (ServiceResolutionException $e) {
        $context = $e->getContext();
        error_log("Service resolution failed for {$serviceId}: " . $context['reason']);
        
        return $default;
    }
}

// Safe service retrieval
$logger = getServiceSafe($container, 'logger', new NullLogger());
$cache = getServiceSafe($container, 'cache', new ArrayCache());
    
```

## Advanced Factory with Context

```php

use Dino\Exceptions\ServiceFactoryException;

class DatabaseFactory implements FactoryInterface {
    public function create(...$params): object {
        $config = $params[0] ?? [];
        
        if (empty($config['host'])) {
            throw new ServiceFactoryException(
                'DatabaseFactory',
                ['reason' => 'Database host configuration missing', 'config' => $config]
            );
        }
        
        return new DatabaseConnection($config);
    }
}

$dbFactory = new DatabaseFactory();

try {
    $database = $dbFactory->create(['host' => 'localhost', 'port' => 3306]);
} catch (ServiceFactoryException $e) {
    echo "Factory Error: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Factory: " . $context['factory_class'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
}
    
```

## Service Container with Validation

```php

// Combine service container with configuration validation
$config = new ConfigHandler();
$config->registerValidator(new RequiredValidator());
$config->setValidationRules([
    'services.database.host' => ['required'],
    'services.database.port' => ['type:int', 'range:1-65535']
]);

try {
    $config->set('services.database.host', 'localhost');
    $config->set('services.database.port', 3306);
    
    // Use validated config in service factory
    $dbConfig = [
        'host' => $config->get('services.database.host'),
        'port' => $config->get('services.database.port')
    ];
    
    $database = $dbFactory->create($dbConfig);
    
} catch (ConfigValidationException $e) {
    echo "Configuration validation failed: " . $e->getErrorMessage() . "\n";
} catch (ServiceFactoryException $e) {
    echo "Service creation failed: " . $e->getErrorMessage() . "\n";
}
    
```

## Best Practices

*   Use service providers for organized service registration
*   Implement lazy loading for expensive services
*   Use service tagging for logical service grouping
*   Always handle service resolution exceptions
*   Combine configuration validation with service creation
*   Use context-aware error reporting for better debugging

## Related Documentation

*   [Service Container Guide](../docs/Guides/service-container-guide.md)
*   [Dependency Resolution Guide](../docs/Guides/dependency-resolution.md)
*   [Error Handling Guide](../docs/Guides/error-handling.md)
*   [ServiceContainer API Reference](../docs/API-Reference/ServiceContainer.md)