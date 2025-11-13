 ServiceWrapperException - Dino Library v1.2.1 

# ServiceWrapperException

`ServiceWrapperException` is thrown when a service wrapper fails to manage or initialize the underlying service correctly. This can occur due to invalid wrapper configuration, failed lazy loading, or runtime errors during service lifecycle management. It extends `ContextAwareException` and provides structured context for debugging and logging.

## 📌 Context Structure

```
{
    "serviceId": "string",
    "wrapper": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ServiceWrapper;
use Dino\Exceptions\ServiceWrapperException;
use Dino\Core\ErrorMessageFormatter;

$wrapper = new ServiceWrapper('cache', function() {
    // Simulate wrapper misconfiguration
    throw new \RuntimeException("Failed to initialize cache service");
});

try {
    $cache = $wrapper->getInstance();
} catch (ServiceWrapperException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[WRAPPER_502] Failed to manage service "cache" in wrapper.
Context: {"serviceId":"cache","wrapper":"ServiceWrapper","reason":"Failed to initialize cache service"}
```

## ✅ Best Practices

*   Ensure wrapper configurations are valid and tested.
*   Log both the service ID and wrapper type when errors occur.
*   Use `ErrorMessageFormatter` for consistent error output.
*   Catch `ServiceWrapperException` to handle wrapper-specific failures gracefully.
*   Provide fallback services or recovery strategies when wrapper initialization fails.