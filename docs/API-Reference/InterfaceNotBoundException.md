 InterfaceNotBoundException - Dino Library v1.2.1 

# InterfaceNotBoundException

`InterfaceNotBoundException` is thrown when the dependency injection container attempts to resolve an interface that has not been bound to any concrete implementation. It extends `ContextAwareException` and provides structured context for debugging and logging.

## 📌 Context Structure

```
{
    "interface": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\InterfaceNotBoundException;
use Dino\Core\ErrorMessageFormatter;

interface LoggerInterface {
    public function log(string $message);
}

$container = new ServiceContainer();

try {
    $logger = $container->get(LoggerInterface::class);
} catch (InterfaceNotBoundException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[IFACE_404] Interface "LoggerInterface" is not bound to any implementation.
Context: {"interface":"LoggerInterface","reason":"No binding found in ServiceContainer"}
```

## ✅ Best Practices

*   Always bind interfaces to concrete implementations before resolving them.
*   Use `singleton()` or `bind()` methods in the container to register services.
*   Log missing interface bindings to identify gaps in configuration.
*   Catch `InterfaceNotBoundException` to provide fallback implementations or meaningful error messages.