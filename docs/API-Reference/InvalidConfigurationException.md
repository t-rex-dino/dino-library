 InvalidConfigurationException - Dino Library v1.2.1

# InvalidConfigurationException

`InvalidConfigurationException` is thrown when a configuration value is invalid, violates rules, or cannot be processed by the system. It extends `ContextAwareException` and provides structured context for debugging.

## Context Structure

```
{
    "configKey": "string",
    "value": "mixed",
    "reason": "string"
}
```

## Usage Example

```php

use Dino\Core\ConfigHandler;
use Dino\Exceptions\InvalidConfigurationException;
use Dino\Core\ErrorMessageFormatter;

$config = new ConfigHandler();

try {
    $config->set('app.port', 'invalid'); // expecting integer
} catch (InvalidConfigurationException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## Sample Output

```
[CONFIG_422] Invalid configuration value for key "app.port".
Context: {"configKey":"app.port","value":"invalid","reason":"Expected integer, got string"}
```

## Best Practices

*   Validate configuration values before setting them.
*   Use validators for complex rules.
*   Log invalid values with context for debugging.
*   Provide defaults or fallback values when possible.