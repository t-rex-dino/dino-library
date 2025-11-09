# Basic Usage Explained

This example demonstrates the foundational usage of Dino Library, including service registration, configuration setup, and container usage. It corresponds to the `examples/basic-usage.php` file.

## Overview

The example initializes the core components of the library:

*   `LibraryManager` – for service registration and retrieval
*   `ConfigHandler` – for managing configuration values
*   `ServiceContainer` – for factory-based service instantiation

## Step-by-Step Breakdown

### 1\. Initialize Core Classes

```php
use Dino\Core\LibraryManager;
use Dino\Core\ConfigHandler;
use Dino\Core\ServiceContainer;

$manager = new LibraryManager();
$config = new ConfigHandler();
$container = new ServiceContainer();
```

These instances are the entry points for using the Dino Library.

### 2\. Register Services

```php
$manager->register('logger', new FileLogger());
$manager->register('mailer', new Mailer());
```

Services are registered with unique identifiers and can be retrieved later.

### 3\. Set Configuration

```php
$config->set('app.name', 'Dino Library');
$config->set('app.debug', true);
```

Configuration values are stored using dot notation for nested keys.

### 4\. Retrieve and Use Services

```php
$logger = $manager->get('logger');
$logger->info('Application started');

$mailer = $manager->get('mailer');
$mailer->send('user@example.com', 'Welcome to Dino Library!');
```

Services are retrieved and used as needed.

### 5\. Inspect Configuration

```php
echo $config->get('app.name'); // Dino Library
echo $config->get('app.debug') ? 'Debug mode' : 'Production mode';
```

## Best Practices Highlighted

*   Use `LibraryManager` for centralized service access
*   Store configuration in `ConfigHandler` for flexibility
*   Use consistent naming for services and config keys
*   Keep service logic decoupled from configuration logic

## Related Files

*   [LibraryManager API Reference](../API-Reference/LibraryManager.md)
*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.md)
*   [Getting Started Tutorial](../Tutorials/getting-started.md)
