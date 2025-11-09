# Config Handler Demo Explained

This example demonstrates how to use the `ConfigHandler` class to manage application configuration. It corresponds to the `examples/config-handler-demo.php` file.

## Overview

The demo showcases:

*   Setting configuration values
*   Retrieving values with defaults
*   Using dot notation for nested keys
*   Loading configuration from array and file
*   Validating configuration entries

## Step-by-Step Breakdown

### 1\. Initialize ConfigHandler

```php
use Dino\Core\ConfigHandler;

$config = new ConfigHandler();
```

### 2\. Set Configuration Values

```php
$config->set('app.name', 'Dino Library');
$config->set('app.debug', true);
$config->set('database.host', 'localhost');
$config->set('database.port', 3306);
```

Values are stored using dot notation for hierarchical access.

### 3\. Retrieve Configuration Values

```php
$appName = $config->get('app.name');
$debugMode = $config->get('app.debug', false);
$dbHost = $config->get('database.host');
```

### 4\. Load Configuration from Array

```php
$config->loadFromArray([
    'cache' => [
        'enabled' => true,
        'driver' => 'redis'
    ]
]);
```

### 5\. Load Configuration from File

```php
$config->loadFromFile('config/app.php');
```

The file must return a valid PHP array.

### 6\. Validate Configuration

```php
$isValid = $config->validate('database.port', function($value) {
    return is_int($value) && $value > 0 && $value < 65536;
});
```

## Output Example

```php
echo "App Name: " . $config->get('app.name') . PHP_EOL;
echo "Debug Mode: " . ($config->get('app.debug') ? 'Enabled' : 'Disabled') . PHP_EOL;
echo "Database Host: " . $config->get('database.host') . PHP_EOL;
```

## Best Practices Highlighted

*   Use dot notation for structured configuration
*   Load config from external files for flexibility
*   Validate critical values before usage
*   Provide defaults to avoid runtime errors

## Related Files

*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.md)
*   [Configuration Management Guide](../Guides/config-management.md)
