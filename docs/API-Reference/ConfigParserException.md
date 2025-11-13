 ConfigParserException - Dino Library v1.2.1

# ConfigParserException

`ConfigParserException` is thrown when a configuration file cannot be parsed due to syntax errors, invalid structure, or unsupported format. It extends `ContextAwareException` and provides structured context data for debugging and logging.

## 📌 Context Structure

```
{
    "file": "string",
    "line": "int",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ConfigLoader;
use Dino\Exceptions\ConfigParserException;
use Dino\Core\ErrorMessageFormatter;

$loader = new ConfigLoader();

try {
    $config = $loader->load('config/app.yaml');
} catch (ConfigParserException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[CONFIG_PARSE_500] Failed to parse configuration file.
Context: {"file":"config/app.yaml","line":12,"reason":"Unexpected token ':' at line 12"}
```

## ✅ Best Practices

*   Validate configuration files with a linter before loading.
*   Use try-catch blocks around config loading to handle parsing errors gracefully.
*   Log the file name, line number, and reason for debugging.
*   Use `ErrorMessageFormatter` for consistent error output.
*   Provide fallback configuration or defaults when parsing fails.