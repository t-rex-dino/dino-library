 DinoException - Dino Library v1.2.1 

# DinoException

`DinoException` is the root base class for all exceptions in Dino Library. It provides a unified type so that developers can catch all Dino-related exceptions in a single block. Most other exceptions extend `DinoException` either directly or indirectly.

## 📌 Usage Example

```php

use Dino\Exceptions\DinoException;
use Dino\Core\ServiceContainer;

try {
    $container = new ServiceContainer();
    $container->get('NonExistentService');
} catch (DinoException $e) {
    echo "Caught a Dino-related exception: " . $e->getMessage();
}
    
```

## 📤 Sample Output

```
Caught a Dino-related exception: Service 'NonExistentService' not found
```

## ✅ Best Practices

*   Use `DinoException` when you want to catch all exceptions from Dino Library in one place.
*   Prefer catching specific exceptions (e.g., `ServiceNotFoundException`) for fine-grained error handling.
*   Use `DinoException` as a fallback to ensure no Dino-related error escapes unhandled.