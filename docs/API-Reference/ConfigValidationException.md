ConfigValidationException API Reference - Dino Library v1.2.1 

# ConfigValidationException

`ConfigValidationException` is thrown when a configuration value fails validation. It extends `ContextAwareException` and provides structured context data for debugging, logging, and user-friendly error reporting.

## Class Synopsis

```php

namespace Dino\Exceptions;

class ConfigValidationException extends ContextAwareException {
    // Inherits all methods from ContextAwareException
    public function getErrorCode(): string;
    public function getErrorMessage(): string;
    public function getContext(): array;
    public function getSeverity(): string;
    public function getFormattedMessage(): string;
}
    
```

## Error Code

`CONFIG_200` - Configuration validation failed

## Context Structure

The exception carries a rich context object with the following fields:

| Field | Type | Description | Required |
| --- | --- | --- | --- |
| `configKey` | string | The configuration key being validated | ✅ Yes |
| `rule` | string | The validation rule that failed | ✅ Yes |
| `reason` | string | Human-readable explanation of the failure | ✅ Yes |
| `errors` | array | List of validation error messages | ❌ Optional |
| `rules` | array | All rules applied to the configuration key | ❌ Optional |
| `value` | mixed | The actual value that failed validation | ❌ Optional |
| `expectedType` | string | Expected data type (for type validation) | ❌ Optional |
| `actualType` | string | Actual data type (for type validation) | ❌ Optional |
| `min` | int/float | Minimum allowed value (for range validation) | ❌ Optional |
| `max` | int/float | Maximum allowed value (for range validation) | ❌ Optional |
| `pattern` | string | Regex pattern (for regex validation) | ❌ Optional |
| `actual` | mixed | Actual numeric value (for range validation) | ❌ Optional |

## Usage Examples

### Basic Validation Error Handling

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\TypeValidator;
use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

$config = new ConfigHandler();
$config->registerValidator(new TypeValidator());
$config->setValidationRules([
    'app.port' => ['type:int']
]);

try {
    $config->set('app.port', 'not_an_integer');
} catch (ConfigValidationException $e) {
    // Basic error message
    echo ErrorMessageFormatter::format($e);
    
    // Output:
    // [CONFIG_200] Configuration validation failed for key "app.port".
    // Context: {"configKey":"app.port","rule":"type:int","reason":"Expected type 'int', got 'string'"}
}
    
```

### Advanced Context Analysis

```php

try {
    $config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    // Access specific context fields
    echo "Validation failed for: " . $context['configKey'] . "\n";
    echo "Failed rule: " . $context['rule'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    echo "Value: " . $context['value'] . "\n";
    
    if (isset($context['min']) && isset($context['max'])) {
        echo "Allowed range: " . $context['min'] . " - " . $context['max'] . "\n";
        echo "Actual value: " . $context['actual'] . "\n";
    }
    
    if (isset($context['expectedType'])) {
        echo "Expected type: " . $context['expectedType'] . "\n";
        echo "Actual type: " . $context['actualType'] . "\n";
    }
    
    // Output:
    // Validation failed for: app.port
    // Failed rule: range:1-65535
    // Reason: Value must be between 1 and 65535
    // Value: 70000
    // Allowed range: 1 - 65535
    // Actual value: 70000
}
    
```

### Multiple Validation Errors

```php

try {
    $config->set('app.port', 'invalid_value');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    if (isset($context['errors']) && is_array($context['errors'])) {
        echo "Multiple validation errors occurred:\n";
        foreach ($context['errors'] as $error) {
            echo " - " . $error . "\n";
        }
    }
    
    if (isset($context['rules'])) {
        echo "Applied rules: " . implode(', ', $context['rules']) . "\n";
    }
}
    
```

### Custom Error Recovery

```php

function setConfigWithRecovery(ConfigHandler $config, string $key, mixed $value, mixed $fallback = null) {
    try {
        $config->set($key, $value);
        return $value;
    } catch (ConfigValidationException $e) {
        $context = $e->getContext();
        
        // Log detailed error information
        error_log("Config validation failed for {$key}: " . $e->getErrorMessage());
        error_log("Context: " . json_encode($context));
        
        // Use fallback value
        if ($fallback !== null) {
            $config->set($key, $fallback);
            return $fallback;
        }
        
        throw $e; // Re-throw if no fallback
    }
}

// Usage with recovery
$port = setConfigWithRecovery($config, 'app.port', 'invalid', 8080);
echo "Using port: " . $port; // 8080
    
```

## Integration with ErrorMessageFormatter

### Formatted Error Output

```php

use Dino\Core\ErrorMessageFormatter;

try {
    $config->set('app.email', 'invalid-email');
} catch (ConfigValidationException $e) {
    // Basic formatted message
    echo ErrorMessageFormatter::format($e);
    // [CONFIG_200] Configuration validation failed for key "app.email".
    
    // Detailed error information
    $details = ErrorMessageFormatter::createDetails($e);
    echo "Error Details:\n";
    echo "Code: " . $details['code'] . "\n";
    echo "Message: " . $details['message'] . "\n";
    echo "Severity: " . $details['severity'] . "\n";
    echo "Category: " . $details['category'] . "\n";
    echo "Timestamp: " . $details['timestamp'] . "\n";
    echo "Context: " . json_encode($details['context'], JSON_PRETTY_PRINT) . "\n";
}
    
```

### Sample Output

```

[CONFIG_200] Configuration validation failed for key "app.email".
Context: {
    "configKey": "app.email",
    "rule": "regex",
    "reason": "Value does not match the required pattern",
    "pattern": "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$/",
    "value": "invalid-email"
}
    
```

## Best Practices

*   **Always include `configKey` and `rule`** in the context for actionable error messages
*   **Use `ErrorMessageFormatter`** to produce consistent, human-readable outputs across your application
*   **Log the full context** for observability and debugging in production environments
*   **Catch `ConfigValidationException` specifically** rather than generic exceptions for better error handling
*   **Provide meaningful `reason` messages** that help users understand and fix the issue
*   **Include relevant validation metadata** like expected vs actual types, ranges, and patterns
*   **Implement graceful error recovery** with fallback values when appropriate

## Related Exceptions

*   [ConfigNotFoundException](ConfigNotFoundException.html) - Thrown when a configuration key is not found
*   [ConfigParserException](ConfigParserException.html) - Thrown when configuration parsing fails
*   [ContextAwareException](ContextAwareException.html) - Base class for all context-aware exceptions

## See Also

*   [ConfigHandler API Reference](ConfigHandler.html)
*   [ValidatorInterface API Reference](ValidatorInterface.html)
*   [Validation System Guide](../Guides/validation-system.html)
*   [Error Handling Guide](../Guides/error-handling.html)