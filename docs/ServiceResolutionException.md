 ServiceResolutionException - Dino Library v1.2.1

# ServiceResolutionException

`ServiceResolutionException` is thrown when the `ServiceContainer` or `DependencyResolver` fails to resolve a requested service. It extends `ContextAwareException` and provides structured context data for debugging and logging.

## Context Structure

The exception carries a JSON-like context object with the following fields:

*   **serviceId** – the identifier of the requested service
*   **reason** – explanation of why resolution failed
*   **dependencies** (optional) – list of unresolved dependencies
*   **attemptedFactory** (optional) – factory or provider used during resolution

## Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\ServiceResolutionException;
use Dino\Core\ErrorMessageFormatter;

$container = new ServiceContainer();

try {
    $logger = $container->get('logger'); // no factory or binding defined
} catch (ServiceResolutionException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## Sample Output

```
[SERVICE_500] Failed to resolve service "logger".
Context: {"serviceId":"logger","reason":"No factory or binding found for this service"}
```

## Best Practices

*   Ensure all required services are registered before resolution.
*   Provide clear factories or bindings for custom services.
*   Catch `ServiceResolutionException` specifically to handle resolution errors gracefully.
*   Log unresolved dependencies for troubleshooting complex service graphs.