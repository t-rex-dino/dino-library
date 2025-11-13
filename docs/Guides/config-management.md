Configuration Management Guide - Dino Library v1.2.1 

# Configuration Management Guide

This guide explains how to manage application configuration using the enhanced `ConfigHandler` class in Dino Library v1.2.1. It covers setting, retrieving, validating, and loading configuration data with advanced error handling and validation integration.

## Introduction

Configuration management is essential for controlling application behavior across environments. Dino Library provides a powerful and flexible `ConfigHandler` class that supports:

*   Dot notation for nested configuration keys
*   Loading from arrays and files
*   Advanced validation with rule parsing
*   Context-aware error reporting
*   Integration with multiple config parsers

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
$isDebug = $config->get('app.debug', false); // with default value
    
```

## Dot Notation

Dot notation allows accessing nested configuration keys easily:

```php

$config->set('database.connections.mysql.host', '127.0.0.1');
$config->set('database.connections.mysql.port', 3306);
$host = $config->get('database.connections.mysql.host');
    
```

## Advanced Validation System (New in v1.2.1)

### Validator Registration

```php

use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());
    
```

### Validation Rules with Rule Parsing

```php

// Define validation rules with intelligent rule parsing
$config->setValidationRules([
    'app.name' => ['required'],
    'app.port' => ['type:int', 'range:1-65535'],
    'app.debug' => ['type:bool'],
    'app.email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
    'database.connections.default.host' => ['required'],
    'database.connections.default.port' => ['type:int', 'range:1-65535']
]);
    
```

### Rule Parsing Intelligence

The system automatically parses rule parameters:

*   `type:int` → validates integer type
*   `range:1-100` → validates numeric range 1-100
*   `regex:/pattern/` → validates against regex pattern

## Error Handling & Reporting (New in v1.2.1)

### Context-Aware Exceptions

```php

use Dino\Exceptions\ConfigNotFoundException;
use Dino\Exceptions\ConfigValidationException;

try {
    $config->set('app.port', 'invalid_string');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    echo "Error Code: " . $e->getErrorCode() . "\n";
    echo "Config Key: " . $context['configKey'] . "\n";
    echo "Failed Rules: " . implode(', ', $context['rules']) . "\n";
    echo "Validation Errors: " . implode('; ', $context['errors']) . "\n";
    echo "Provided Value: " . $context['value'] . "\n";
}
    
```

### Configuration Not Found

```php

try {
    $value = $config->get('non.existent.key');
} catch (ConfigNotFoundException $e) {
    echo "Configuration key not found: " . $e->getErrorMessage() . "\n";
    $context = $e->getContext();
    echo "Available keys: " . implode(', ', $context['available_keys'] ?? []) . "\n";
}
    
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

## Loading Configuration from Files

### JSON Configuration

```php

use Dino\Core\ConfigLoader;
use Dino\Core\JsonConfigParser;

$loader = new ConfigLoader();
$loader->registerParser('json', new JsonConfigParser());

$configData = $loader->load('config/app.json');
foreach ($configData as $key => $value) {
    $config->set($key, $value);
}
    
```

### YAML Configuration

```php

use Dino\Core\YamlConfigParser;

$loader->registerParser('yaml', new YamlConfigParser());
$configData = $loader->load('config/database.yaml');
    
```

## Performance Optimization

### Cached Configuration Loading

```php

use Dino\Core\CachedConfigLoader;
use Dino\Core\ArrayCache;

$cache = new ArrayCache();
$cachedLoader = new CachedConfigLoader($loader, $cache);
$configData = $cachedLoader->load('config/app.json');
    
```

## Best Practices

### Configuration Structure

```php

// Recommended structure
$config->setValidationRules([
    'app.name' => ['required'],
    'app.version' => ['required', 'regex:/^\d+\.\d+\.\d+$/'],
    'app.debug' => ['type:bool'],
    
    'database.default.host' => ['required'],
    'database.default.port' => ['type:int', 'range:1-65535'],
    'database.default.database' => ['required'],
    
    'cache.default.driver' => ['required'],
    'cache.default.host' => ['required'],
    'cache.default.port' => ['type:int', 'range:1-65535']
]);
    
```

### Error Recovery Strategies

```php

function getConfigWithFallback(ConfigHandler $config, string $key, mixed $default = null) {
    try {
        return $config->get($key);
    } catch (ConfigNotFoundException $e) {
        return $default;
    } catch (ConfigValidationException $e) {
        // Log validation error and return default
        error_log("Config validation failed for {$key}: " . $e->getErrorMessage());
        return $default;
    }
}

$port = getConfigWithFallback($config, 'app.port', 8080);
    
```

## Migration from Previous Versions

All existing code continues to work with enhanced error messages and context-aware exceptions.

## Related Components

*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.md)
*   [Validation System Guide](validation-system.md)
*   [Error Handling Guide](error-handling.md)
*   [Installation Guide](installation.md)