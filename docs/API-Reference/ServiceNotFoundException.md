 ServiceNotFoundException - Dino Library v1.2.1

# ServiceNotFoundException

`ServiceNotFoundException` is thrown when a requested service ID does not exist in the `ServiceContainer`. It extends `ContextAwareException` and provides structured context data for debugging and logging.

## Context Structure

The exception carries a JSON-like context object with the following fields:

*   **serviceId** – the missing service identifier
*   **reason** – explanation of why the service was not found

## Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\ServiceNotFoundException;
use Dino\Core\ErrorMessageFormatter;

$container = new ServiceContainer();

try {
    $mailer = $container->get('mailer'); // service not registered
} catch (ServiceNotFoundException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## Sample Output

```
[SERVICE_404] Service with ID "mailer" was not found.
Context: {"serviceId":"mailer","reason":"No service registered under this ID"}
```

## Best Practices

*   Always check if a service is registered using `$container->has()` before accessing it.
*   Catch `ServiceNotFoundException` specifically to handle missing services gracefully.
*   Log the structured context for observability and debugging.
*   Provide fallback mechanisms or default services when critical services are missing.