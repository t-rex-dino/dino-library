Error Handling Guide - Dino Library v1.2.1 

# Error Handling System

This guide explains the new context-aware exception system introduced in Dino Library v1.2.1, providing standardized error codes, structured context data, and consistent exception hierarchy for better debugging and integration.

## Introduction

The error handling system in Dino Library v1.2.1 provides a comprehensive framework for managing errors with rich context information, standardized error codes, and consistent formatting across all components.

## Core Components

### ErrorMessageInterface

Standardized contract for all context-aware exceptions:

```php

interface ErrorMessageInterface {
    public function getErrorCode(): string;
    public function getErrorMessage(): string;
    public function getContext(): array;
    public function getSeverity(): string;
    public function getFormattedMessage(): string;
}
    
```

### ErrorMessageFormatter

Utility class for consistent error message formatting and detailed error analysis:

```php

use Dino\Core\ErrorMessageFormatter;

$formatted = ErrorMessageFormatter::format($exception);
$details = ErrorMessageFormatter::createDetails($exception);
$validationErrors = ErrorMessageFormatter::formatValidationErrors($errors);
    
```

## Error Codes

| Error Code | Category | Description | Severity |
| --- | --- | --- | --- |
| `CONFIG_200` | Configuration | Configuration validation error | HIGH |
| `CONFIG_201` | Configuration | Configuration key not found | MEDIUM |
| `CONFIG_202` | Configuration | Configuration parser error | HIGH |
| `SERVICE_100` | Service | Service not found in container | MEDIUM |
| `SERVICE_101` | Service | Circular dependency detected | CRITICAL |
| `SERVICE_102` | Service | Service resolution failure | HIGH |
| `DI_300` | Dependency Injection | Unresolvable parameter | HIGH |
| `DI_301` | Dependency Injection | Missing type hint | MEDIUM |
| `VALIDATION_400` | Validation | Validation failed | MEDIUM |
| `LAZY_500` | Lazy Loading | Lazy loading failure | HIGH |

## Exception Hierarchy

```

ContextAwareException
├── ConfigValidationException
├── ConfigNotFoundException
├── ConfigParserException
├── ServiceNotFoundException
├── ServiceResolutionException
├── ServiceFactoryException
├── CircularDependencyException
├── UnresolvableParameterException
├── MissingTypeHintException
├── InterfaceNotBoundException
├── LazyLoadingException
├── ServiceWrapperException
├── ValidationException
└── ValidatorNotFoundException
    
```

## Context Structure

All context-aware exceptions include rich structured data for comprehensive debugging:

### Common Context Fields

*   `configKey` - Configuration key being validated
*   `rule` - Validation rule that failed
*   `reason` - Human-readable failure explanation
*   `value` - The actual value that failed validation
*   `errors` - Array of validation error messages
*   `rules` - All rules applied to the configuration key

### Type-Specific Context Fields

*   **Type Validation:** `expectedType`, `actualType`
*   **Range Validation:** `min`, `max`, `actual`
*   **Regex Validation:** `pattern`
*   **Circular Dependencies:** `dependency_chain`, `depth`
*   **Service Resolution:** `available_services`, `bindings`

## Usage Examples

### Basic Validation Error Handling

```php

use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

try {
    $validator->validate("abc", ["rule" => "type:int", "configKey" => "db.port"]);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

### Advanced Context Analysis

```php

try {
    $config->set('app.port', 70000);
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    echo "Error Code: " . $e->getErrorCode() . "\n";
    echo "Severity: " . $e->getSeverity() . "\n";
    echo "Config Key: " . $context['configKey'] . "\n";
    echo "Failed Rule: " . $context['rule'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    
    if (isset($context['min']) && isset($context['max'])) {
        echo "Allowed Range: " . $context['min'] . " - " . $context['max'] . "\n";
        echo "Actual Value: " . $context['actual'] . "\n";
    }
}
    
```

### Detailed Error Information

```php

use Dino\Core\ErrorMessageFormatter;

try {
    $service = $container->get('unknown_service');
} catch (ServiceNotFoundException $e) {
    $details = ErrorMessageFormatter::createDetails($e);
    
    echo "Detailed Error Information:\n";
    echo "Code: " . $details['code'] . "\n";
    echo "Message: " . $details['message'] . "\n";
    echo "Severity: " . $details['severity'] . "\n";
    echo "Category: " . $details['category'] . "\n";
    echo "Timestamp: " . $details['timestamp'] . "\n";
    echo "Context: " . json_encode($details['context'], JSON_PRETTY_PRINT) . "\n";
}
    
```

## Integration with Logging Systems

### Structured Logging

```php

try {
    $config->set('app.email', 'invalid-email');
} catch (ConfigValidationException $e) {
    // Structured logging with context
    $logger->error('Configuration validation failed', [
        'error_code' => $e->getErrorCode(),
        'config_key' => $e->getContext()['configKey'],
        'rule' => $e->getContext()['rule'],
        'reason' => $e->getContext()['reason'],
        'severity' => $e->getSeverity(),
        'timestamp' => date('c')
    ]);
    
    // Or use ErrorMessageFormatter for consistency
    $logger->error(ErrorMessageFormatter::format($e), [
        'context' => $e->getContext(),
        'category' => $e->getErrorCode()
    ]);
}
    
```

### Monolog Integration

```php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('dino_library');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));

try {
    $resolver->resolve(ComplexService::class);
} catch (CircularDependencyException $e) {
    $log->error($e->getErrorMessage(), $e->getContext());
}
    
```

## Error Recovery Strategies

### Graceful Fallback Pattern

```php

function getConfigWithFallback(ConfigHandler $config, string $key, mixed $default = null) {
    try {
        return $config->get($key);
    } catch (ConfigNotFoundException $e) {
        // Log and return default value
        error_log("Configuration key not found: {$key}, using default");
        return $default;
    } catch (ConfigValidationException $e) {
        // Log validation error and return default
        error_log("Configuration validation failed for {$key}: " . $e->getErrorMessage());
        return $default;
    }
}

// Usage
$port = getConfigWithFallback($config, 'app.port', 8080);
$host = getConfigWithFallback($config, 'app.host', 'localhost');
    
```

### Service Resolution with Fallback

```php

function getServiceWithFallback(ServiceContainer $container, string $serviceId, mixed $fallback = null) {
    try {
        return $container->get($serviceId);
    } catch (ServiceNotFoundException $e) {
        // Log and return fallback service
        error_log("Service not found: {$serviceId}, using fallback");
        return $fallback;
    } catch (ServiceResolutionException $e) {
        // Log resolution error and return fallback
        $context = $e->getContext();
        error_log("Service resolution failed for {$serviceId}: " . $context['reason']);
        return $fallback;
    }
}

// Usage
$logger = getServiceWithFallback($container, 'logger', new NullLogger());
$cache = getServiceWithFallback($container, 'cache', new ArrayCache());
    
```

## Sample Output

### Formatted Error Message

```

[CONFIG_200] Configuration validation failed for key "db.port".
Context: {
    "configKey": "db.port",
    "rule": "type:int",
    "reason": "Expected type 'int', got 'string'",
    "value": "abc",
    "expectedType": "int",
    "actualType": "string"
}
    
```

### Circular Dependency Error

```

[SERVICE_101] Circular dependency detected for service "ServiceA".
Context: {
    "dependency_chain": ["ServiceA", "ServiceB", "ServiceC", "ServiceA"],
    "depth": 3,
    "resolution_stack": [
        "ServiceA::__construct",
        "ServiceB::__construct", 
        "ServiceC::__construct"
    ]
}
    
```

## Best Practices

*   **Always throw specific exceptions** with full context information
*   **Use `ConfigValidationException`** in validators with complete validation context
*   **Use `ConfigNotFoundException`** when a required configuration key is missing
*   **Use `ServiceNotFoundException`** or `ServiceResolutionException` for DI container errors
*   **Leverage `ErrorMessageFormatter`** for structured logging and consistent user feedback
*   **Include relevant metadata** in exception context for comprehensive debugging
*   **Implement graceful error recovery** with appropriate fallback strategies
*   **Use standardized error codes** for consistent error categorization and handling
*   **Log complete context information** for production debugging and monitoring

## Related Documentation

*   [ConfigValidationException API Reference](../API-Reference/ConfigValidationException.html)
*   [ErrorMessageFormatter API Reference](../API-Reference/ErrorMessageFormatter.html)
*   [Validation System Guide](validation-system.html)
*   [Configuration Management Guide](config-management.html)
*   [ErrorCodes Reference](../API-Reference/ErrorCodes.html)