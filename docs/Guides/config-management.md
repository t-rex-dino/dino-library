# Configuration Management Guide

This guide explains how to manage application configuration using the `ConfigHandler` class in Dino Library. It covers setting, retrieving, validating, and loading configuration data.

## Introduction

Configuration management is essential for controlling application behavior across environments. Dino Library provides a powerful and flexible `ConfigHandler` class that supports:

*   Dot notation for nested configuration keys
*   Loading from arrays and files
*   Validation of configuration values
*   Merging and overriding configurations

## Basic Usage

```php
use Dino\Core\ConfigHandler;

$config = new ConfigHandler();

// Set configuration values
$config->set('app.name', 'Dino Library');
$config->set('app.debug', true);
$config->set('database.host', 'localhost');

// Retrieve values
$appName = $config->get('app.name');
$isDebug = $config->get('app.debug', false);
```

## Dot Notation

Dot notation allows accessing nested configuration keys easily:

```php
$config->set('database.connections.mysql.host', '127.0.0.1');
$host = $config->get('database.connections.mysql.host');
```

## Loading Configuration from Array

```php
$config->loadFromArray([
    'app' => [
        'name' => 'My App',
        'version' => '1.0.0'
    ],
    'cache' => [
        'enabled' => true,
        'driver' => 'redis'
    ]
]);
```

## Loading Configuration from File

You can load configuration from a PHP file that returns an array:

```php
// config/app.php
return [
    'app' => [
        'name' => 'My App',
        'debug' => true
    ]
];

// Load into handler
$config->loadFromFile('config/app.php');
```

## Validation

Validate configuration values using custom logic:

```php
$isValid = $config->validate('app.name', function($value) {
    return is_string($value) && strlen($value) > 0;
});
```

## Merging Configurations

Merge new configuration data into existing settings:

```php
$config->merge([
    'app' => [
        'debug' => false
    ]
]);
```

## Removing Configuration Keys

```php
$config->remove('cache.driver');
```

## Best Practices

*   Use dot notation for organized configuration hierarchy
*   Load environment-specific settings from external files
*   Validate critical configuration values during bootstrap
*   Provide default values for optional keys
*   Use `merge()` to override settings without losing existing data

## Related Components

*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.md)
*   [Installation Guide](installation.md)
*   [Service Container Guide](service-container-guide.md)
