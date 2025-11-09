# Service Container Guide

This guide explains how to use the `ServiceContainer` class in Dino Library to manage dependency injection and service factories.

## Introduction

The `ServiceContainer` is a flexible and extensible container for managing service creation and retrieval. It supports:

*   Factory-based service instantiation
*   Lazy loading of services
*   Parameterized service creation
*   Custom factory implementations

## Basic Usage

```php
use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new FileLogger();
    }
}

$container = new ServiceContainer();
$container->addFactory('logger', new LoggerFactory());

$logger = $container->get('logger');
```

## Registering Factories

Factories must implement the `FactoryInterface` and define a `create()` method:

```php
interface FactoryInterface {
    public function create(...$params): object;
}
```

Register a factory with a unique service name:

```php
$container->addFactory('mailer', new MailerFactory());
```

## Retrieving Services

Once registered, services can be retrieved using `get()`:

```php
$mailer = $container->get('mailer');
```

## Passing Parameters to Factories

You can pass parameters when retrieving a service:

```php
$db = $container->get('database', 'localhost', 'root', 'secret');
```

The parameters will be forwarded to the factory's `create()` method.

## Checking Service Availability

```php
if ($container->has('logger')) {
    $logger = $container->get('logger');
}
```

## Removing Services

```php
$container->remove('mailer');
```

## Best Practices

*   Use descriptive service names for clarity
*   Keep factories stateless and reusable
*   Use parameterized creation for configurable services
*   Check service existence before usage
*   Remove unused services to free resources

## Related Components

*   [ServiceContainer API Reference](../API-Reference/ServiceContainer.md)
*   [FactoryInterface](../API-Reference/FactoryInterface.md)
*   [Configuration Management Guide](config-management.md)
