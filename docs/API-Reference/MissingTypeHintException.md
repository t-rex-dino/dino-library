 MissingTypeHintException - Dino Library v1.2.1

# MissingTypeHintException

`MissingTypeHintException` is thrown when the dependency resolver encounters a parameter without a type hint, making it impossible to resolve the dependency automatically. This typically occurs in constructors or methods where parameters lack type declarations.

## 📌 Context Structure

```
{
    "parameter": "string",
    "class": "string",
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\DependencyResolver;
use Dino\Exceptions\MissingTypeHintException;
use Dino\Core\ErrorMessageFormatter;

class Logger {
    public function __construct($driver) {} // missing type hint
}

$resolver = new DependencyResolver();

try {
    $logger = $resolver->resolve(Logger::class);
} catch (MissingTypeHintException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[TYPEHINT_400] Cannot resolve parameter "$driver" in class "Logger".
Context: {"parameter":"driver","class":"Logger","reason":"Missing type hint for automatic resolution"}
```

## ✅ Best Practices

*   Always declare type hints for constructor and method parameters.
*   Use interfaces for dependencies to allow flexible implementations.
*   Provide default values for optional parameters when possible.
*   Use `ErrorMessageFormatter` to generate consistent error messages.
*   Log missing type hints to identify and fix resolution issues early.