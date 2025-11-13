 ServiceFactoryException - Dino Library v1.2.1 

# ServiceFactoryException

`ServiceFactoryException` is thrown when a service factory fails to create a valid instance. This can occur due to runtime errors inside the factory, invalid return values, or missing dependencies. It extends `ContextAwareException` and provides structured context for debugging and logging.

## 📌 Context Structure

```
{
    "serviceId": "string",
    "factory": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\ServiceFactoryException;
use Dino\Core\ErrorMessageFormatter;

$container = new ServiceContainer();

$container->factory('Mailer', function($c) {
    // Simulate failure
    throw new \RuntimeException("SMTP configuration missing");
});

try {
    $mailer = $container->get('Mailer');
} catch (ServiceFactoryException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[FACTORY_501] Failed to create service "Mailer" using factory.
Context: {"serviceId":"Mailer","factory":"Closure","reason":"SMTP configuration missing"}
```

## ✅ Best Practices

*   Ensure factories return valid, fully initialized instances.
*   Catch and log runtime errors inside factories.
*   Provide meaningful error messages when factory creation fails.
*   Use `ErrorMessageFormatter` for consistent output.
*   Consider fallback factories or default services for resilience.