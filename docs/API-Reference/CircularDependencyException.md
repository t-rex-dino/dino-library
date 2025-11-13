 CircularDependencyException - Dino Library v1.2.1

# CircularDependencyException

`CircularDependencyException` is thrown when the dependency resolver detects a circular reference between services. This typically occurs when Service A depends on Service B, and Service B depends back on Service A directly or indirectly.

## 📌 Context Structure

```
{
    "cycle": ["string"],
    "reason": "string"
}
```

## 🧪 Usage Example

```php

use Dino\Core\ServiceContainer;
use Dino\Exceptions\CircularDependencyException;
use Dino\Core\ErrorMessageFormatter;

$container = new ServiceContainer();

$container->singleton('A', function($c) {
    return new A($c->get('B'));
});

$container->singleton('B', function($c) {
    return new B($c->get('A'));
});

try {
    $container->get('A');
} catch (CircularDependencyException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 📤 Sample Output

```
[CIRCULAR_100] Circular dependency detected.
Context: {"cycle":["A","B","A"],"reason":"Service 'A' depends on 'B' which depends back on 'A'"}
```

## ✅ Best Practices

*   Design services to be loosely coupled and avoid mutual dependencies.
*   Use interfaces and abstraction to break dependency cycles.
*   Consider event-driven or observer patterns instead of direct service calls.
*   Use dependency graphs or visualization tools to detect cycles early.
*   Log and monitor service resolution paths to identify problematic patterns.