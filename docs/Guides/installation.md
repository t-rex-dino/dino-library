# Installation Guide

This guide explains how to install and set up the Dino Library in your PHP project using Composer.

## Requirements

*   PHP 8.1 or higher
*   Composer installed

## Installation via Composer

```bash
composer require t-rex-dino/dino-library
```

This will download the library and its dependencies into your project's `vendor/` directory.

## Directory Structure After Installation

```
your-project/
├── vendor/
│   └── t-rex-dino/dino-library/
├── composer.json
└── ...
```

## Autoloading

Dino Library follows PSR-4 autoloading. Composer will automatically register the `Dino` namespace.

```php
require 'vendor/autoload.php';

use Dino\Core\LibraryManager;
```

## Basic Setup

After installation, you can start using the core components:

```php
use Dino\Core\LibraryManager;
use Dino\Core\ConfigHandler;
use Dino\Core\ServiceContainer;

$manager = new LibraryManager();
$config = new ConfigHandler();
$container = new ServiceContainer();
```

## Verifying Installation

To verify the installation, run a simple script:

```php
// test.php
require 'vendor/autoload.php';

use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('test', new stdClass());

echo "Service registered: " . ($manager->has('test') ? 'Yes' : 'No');
```

Run it using:

```bash
php test.php
```

## Next Steps

*   Configure your application using `ConfigHandler`
*   Manage dependencies with `ServiceContainer`
*   Explore examples in the `examples/` directory

## Related Guides

*   [Configuration Management](config-management.md)
*   [Service Container Guide](service-container-guide.md)
