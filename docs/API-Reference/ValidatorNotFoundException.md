 ValidatorNotFoundException - Dino Library v1.2.1

# ValidatorNotFoundException

`ValidatorNotFoundException` is thrown when a requested validation rule does not have a registered validator in the `ValidatorRegistry`. It extends `ContextAwareException` and provides structured context data for debugging and logging.

## 📌 Context Structure

```
{
    "rule": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ValidatorRegistry;
use Dino\Exceptions\ValidatorNotFoundException;
use Dino\Core\ErrorMessageFormatter;

$registry = new ValidatorRegistry();

try {
    $registry->validate('custom:foo', 'bar', ['configKey' => 'app.custom']);
} catch (ValidatorNotFoundException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[VALIDATOR_404] Validator for rule "custom:foo" was not found.
Context: {"rule":"custom:foo","reason":"No validator registered for this rule"}
```

## ✅ Best Practices

*   Ensure all required validators are registered before validation begins.
*   Use `ValidatorRegistry::hasValidator()` to check availability.
*   Catch `ValidatorNotFoundException` and log the rule and reason.
*   Use `ErrorMessageFormatter` for consistent error output.
*   Consider fallback validation or default rules for optional config keys.