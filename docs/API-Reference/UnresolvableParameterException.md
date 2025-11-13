 UnresolvableParameterException - Dino Library v1.2.1

# UnresolvableParameterException

`UnresolvableParameterException` is thrown when the dependency resolver is unable to resolve a constructor or method parameter due to missing type hints, unregistered services, or lack of default values. It extends `ContextAwareException` and provides structured context data for debugging and logging.

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
use Dino\Exceptions\UnresolvableParameterException;
use Dino\Core\ErrorMessageFormatter;

class Foo {
    public function __construct($bar) {} // no type hint
}

$resolver = new DependencyResolver();

try {
    $foo = $resolver->resolve(Foo::class);
} catch (UnresolvableParameterException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[PARAM_422] Cannot resolve parameter "$bar" in class "Foo".
Context: {"parameter":"bar","class":"Foo","reason":"Missing type hint or default value"}
```

## ✅ Best Practices

*   Always use explicit type hints for constructor parameters.
*   Register all required services in the container before resolution.
*   Provide default values for optional parameters.
*   Use interfaces instead of concrete classes for better flexibility.
*   Catch `UnresolvableParameterException` and log the parameter and class for debugging.
*   Use `ErrorMessageFormatter` for consistent error output.