 DependencyInjectionException - Dino Library v1.2.1

# DependencyInjectionException

`DependencyInjectionException` is thrown when the dependency injection container fails to inject a required dependency. This can occur due to missing bindings, incompatible type hints, or circular references. It extends `ContextAwareException` and provides structured context for debugging and logging.

## 📌 Context Structure

```
{
    "serviceId": "string",
    "dependency": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\DependencyInjectionException;
use Dino\Core\ErrorMessageFormatter;

$container = new ServiceContainer();

$container->singleton('Mailer', function($c) {
    // Missing dependency "Logger"
    return new Mailer($c->get('Logger'));
});

try {
    $mailer = $container->get('Mailer');
} catch (DependencyInjectionException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[DI_500] Failed to inject dependency "Logger" into service "Mailer".
Context: {"serviceId":"Mailer","dependency":"Logger","reason":"No binding found for Logger"}
```

## ✅ Best Practices

*   Register all required dependencies before resolving services.
*   Use explicit type hints to help the resolver identify dependencies.
*   Log missing or incompatible dependencies with full context.
*   Break circular references by introducing abstractions or factories.
*   Catch `DependencyInjectionException` specifically to handle injection failures gracefully.