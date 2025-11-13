 ConfigNotFoundException - Dino Library v1.2.1

# ConfigNotFoundException

`ConfigNotFoundException` is thrown when a requested configuration key cannot be found in the `ConfigHandler`. It extends `ContextAwareException` and provides structured context data for debugging and logging.

## Context Structure

The exception carries a JSON-like context object with the following fields:

*   **configKey** – the missing configuration key
*   **reason** – explanation of why the key was not found
*   **source** (optional) – the configuration source (e.g., file, environment)

## Usage Example

```php

use Dino\Core\ConfigHandler;
use Dino\Exceptions\ConfigNotFoundException;
use Dino\Core\ErrorMessageFormatter;

$config = new ConfigHandler();

try {
    $value = $config->get('db.password');
} catch (ConfigNotFoundException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## Sample Output

```
[CONFIG_404] Configuration key "db.password" not found.
Context: {"configKey":"db.password","reason":"Key does not exist in current configuration source"}
```

## Best Practices

*   Always check for required keys before accessing them.
*   Use default values or fallback mechanisms to avoid runtime errors.
*   Log missing keys with full context for troubleshooting.
*   Catch `ConfigNotFoundException` specifically to handle missing configuration gracefully.