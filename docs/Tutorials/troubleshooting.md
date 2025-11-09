# Troubleshooting Dino Library

This guide helps you identify and resolve common issues when working with Dino Library, including configuration errors, missing services, and factory failures.

## Service Not Found

**Symptom:** Calling `get()` throws a `ServiceException`

```php
$service = $manager->get('nonexistent');
```

**Solution:**

*   Check if the service was registered using `has()`
*   Ensure the service name is spelled correctly
*   Verify registration logic during application bootstrap

## Configuration Key Missing

**Symptom:** `get()` returns `null` or default unexpectedly

```php
$value = $config->get('app.name'); // returns null
```

**Solution:**

*   Use `has()` to check key existence
*   Ensure the key was set or loaded correctly
*   Check file path and format if using `loadFromFile()`

## Factory Creation Failure

**Symptom:** `get()` throws an exception during service creation

```php
$service = $container->get('mailer'); // fails
```

**Solution:**

*   Check the factory's `create()` method for errors
*   Ensure required parameters are passed correctly
*   Use try-catch to handle failures gracefully

## Configuration File Not Found

**Symptom:** `loadFromFile()` throws `ConfigurationException`

```php
$config->loadFromFile('missing.php');
```

**Solution:**

*   Verify the file path and extension
*   Ensure the file returns a valid array
*   Use `file_exists()` before loading

## Debugging Tips

*   Enable verbose logging during development
*   Use `getAll()` to inspect registered services and configuration
*   Isolate failing components in test scripts
*   Use PHPUnit and Mockery for unit testing

## Example: Safe Service Access

```php
if ($manager->has('logger')) {
    $logger = $manager->get('logger');
    $logger->info('Logger is available');
} else {
    error_log('Logger service not found');
}
```

## Related Resources

*   [Getting Started Tutorial](getting-started.md)
*   [Advanced Usage Tutorial](advanced-usage.md)
*   [ServiceContainer API Reference](../API-Reference/ServiceContainer.md)
