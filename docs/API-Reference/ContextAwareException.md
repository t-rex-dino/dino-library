 ContextAwareException - Dino Library v1.2.1 

# ContextAwareException

`ContextAwareException` is the abstract base class for all context-aware exceptions in Dino Library. It extends `DinoException` and provides a standardized way to attach structured context data to exceptions, ensuring consistent error reporting and debugging across the framework.

## 📌 Context Structure

Each subclass defines its own context keys, but the base class guarantees that context is always represented as a JSON-like structure.

```
{
    "key": "value",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Exceptions\ContextAwareException;

class CustomException extends ContextAwareException {
    public function __construct(string $message, array $context = []) {
        parent::__construct($message, $context);
    }
}

try {
    throw new CustomException("Something went wrong", ["operation" => "save", "reason" => "Disk full"]);
} catch (ContextAwareException $e) {
    echo $e->getMessage();
    print_r($e->getContext());
}
    
```

## 📤 Sample Output

```
Something went wrong
Context: {"operation":"save","reason":"Disk full"}
```

## ✅ Best Practices

*   Always include actionable context keys (e.g., `operation`, `serviceId`, `rule`).
*   Use `ErrorMessageFormatter` to produce consistent error messages from context-aware exceptions.
*   Extend `ContextAwareException` for all custom exceptions to ensure uniformity.
*   Log both the message and context for effective debugging.