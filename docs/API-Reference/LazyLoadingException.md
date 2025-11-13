 LazyLoadingException - Dino Library v1.2.1 

# LazyLoadingException

`LazyLoadingException` is thrown when the lazy loading mechanism fails to initialize or retrieve a service instance. Typical causes include missing factory bindings, failed initialization, or invalid wrapper configuration. It extends `ContextAwareException` and provides structured context for debugging and logging.

## Context structure

```
{
"serviceId": "string",
"reason": "string"
}
```

## Usage example

```php

use Dino\Core\LazyServiceWrapper;
use Dino\Exceptions\LazyLoadingException;
use Dino\Core\ErrorMessageFormatter;

$wrapper = new LazyServiceWrapper('logger', function () {
    // Simulate an initialization failure
    throw new \RuntimeException("Failed to initialize service");
});

try {
    $service = $wrapper->getInstance();
} catch (LazyLoadingException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## Sample output

```
[LAZY_503] Failed to lazy load service "logger".
Context: {"serviceId":"logger","reason":"Initialization failed in LazyServiceWrapper"}
```

## Best practices

*   **Register factories:** Ensure a valid factory/provider is bound before lazy access.
*   **Fail fast:** Surface initialization errors early with clear reasons.
*   **Log context:** Always log `serviceId` and `reason` for troubleshooting.
*   **Fallbacks:** Provide default or degraded services when possible.
*   **Tests:** Add unit tests for lazy wrappers and initialization paths.