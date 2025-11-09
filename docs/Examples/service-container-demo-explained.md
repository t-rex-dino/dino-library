# Service Container Demo Explained

This example demonstrates how to use the `ServiceContainer` class to manage service factories and retrieve services dynamically. It corresponds to the `examples/service-container-demo.php` file.

## Overview

The demo showcases:

*   Implementing a factory class
*   Registering the factory in the container
*   Retrieving services via the container
*   Passing parameters to factories

## Step-by-Step Breakdown

### 1\. Define a Factory Class

```php
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new FileLogger();
    }
}
```

This class implements `FactoryInterface` and returns a new instance of `FileLogger`.

### 2\. Initialize ServiceContainer

```php
use Dino\Core\ServiceContainer;

$container = new ServiceContainer();
```

### 3\. Register the Factory

```php
$container->addFactory('logger', new LoggerFactory());
```

The factory is registered with a unique service name.

### 4\. Retrieve the Service

```php
$logger = $container->get('logger');
$logger->info('ServiceContainer demo started');
```

The container uses the factory to create and return the service instance.

### 5\. Check Service Availability

```php
if ($container->has('logger')) {
    echo "Logger service is available";
}
```

### 6\. Remove a Service

```php
$container->remove('logger');
```

This removes the factory and its associated service from the container.

## Best Practices Highlighted

*   Keep factories simple and stateless
*   Use descriptive service names
*   Check availability before accessing services
*   Remove unused services to optimize memory

## Related Files

*   [ServiceContainer API Reference](../API-Reference/ServiceContainer.md)
*   [FactoryInterface API Reference](../API-Reference/FactoryInterface.md)
*   [Service Container Guide](../Guides/service-container-guide.md)
