# Getting Started with Dino Library

This tutorial walks you through the initial setup and basic usage of Dino Library. By the end, you'll be able to register services, manage configuration, and use the service container effectively.

## Step 1: Install the Library

```bash
composer require t-rex-dino/dino-library
```

This will install the library and its dependencies into your project.

## Step 2: Autoload and Initialize

```php
require 'vendor/autoload.php';

use Dino\Core\LibraryManager;
use Dino\Core\ConfigHandler;
use Dino\Core\ServiceContainer;

$manager = new LibraryManager();
$config = new ConfigHandler();
$container = new ServiceContainer();
```

## Step 3: Register and Retrieve Services

```php
$manager->register('logger', new FileLogger());
$logger = $manager->get('logger');
```

## Step 4: Manage Configuration

```php
$config->set('app.name', 'Dino Library');
echo $config->get('app.name');
```

## Step 5: Use Service Container

```php
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new FileLogger();
    }
}

$container->addFactory('logger', new LoggerFactory());
$logger = $container->get('logger');
```

## Step 6: Run Example Files

Explore the `examples/` directory:

```bash
php examples/basic-usage.php
php examples/config-handler-demo.php
php examples/service-container-demo.php
```

## Next Steps

*   Read the [Configuration Management Guide](../Guides/config-management.md)
*   Explore [Service Container Guide](../Guides/service-container-guide.md)
*   Check [LibraryManager API Reference](../API-Reference/LibraryManager.md)
