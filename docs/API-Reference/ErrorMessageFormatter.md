 ErrorMessageFormatter - Dino Library v1.2.1

# ErrorMessageFormatter

`ErrorMessageFormatter` is a utility class designed to format context-aware exceptions into structured, human-readable error messages. It extracts the error code, message, and context from exceptions implementing the `ContextualExceptionInterface`.

## 🎯 Purpose

Provides consistent error output across CLI tools, web responses, and logs. Ensures exceptions are actionable and traceable.

## 🧪 Usage Example

```php

use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

$exception = new ConfigValidationException(
    'Validation failed for config key "app.name".',
    'CONFIG_200',
    ['rule' => 'required', 'reason' => 'Value is required and cannot be empty']
);

echo ErrorMessageFormatter::format($exception);
    
```

## 📤 Sample Output

```
[CONFIG_200] Validation failed for config key "app.name".
Context: {"rule":"required","reason":"Value is required and cannot be empty"}
```

## ✅ Best Practices

*   Use `ErrorMessageFormatter::format()` for all exceptions implementing `ContextualExceptionInterface`.
*   Ensure each exception includes a unique error code and structured context array.
*   Log formatted messages for consistent debugging and monitoring.
*   Use the formatter in CLI tools, web responses, and logs for unified error output.
*   Do not expose raw exception traces in production; use formatted output instead.