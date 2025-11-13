Troubleshooting Guide - Dino Library v1.2.1 

# Troubleshooting Guide - Dino Library v1.2.1

This guide helps you identify and resolve common issues when using Dino Library v1.2.1 with the new error handling and validation systems.

## Quick Navigation

*   [Configuration Issues](#configuration-issues)
*   [Validation Issues](#validation-issues)
*   [Service Container Issues](#service-container-issues)
*   [Dependency Injection Issues](#dependency-injection-issues)
*   [Error Handling Issues](#error-handling-issues)

## 🔧 Configuration Issues

### Problem: "Configuration key not found" error

```php

// Error: [CONFIG_201] Configuration key "app.unknown_key" not found
    
```

#### Solution:

```php

use Dino\Exceptions\ConfigNotFoundException;

try {
    $value = $config->get('app.unknown_key');
} catch (ConfigNotFoundException $e) {
    // Check available keys
    $context = $e->getContext();
    $availableKeys = $context['available_keys'] ?? [];
    
    // Use has() to check before get()
    if ($config->has('app.name')) {
        $value = $config->get('app.name');
    } else {
        // Provide default value
        $value = 'Default App Name';
    }
}
    
```

### Problem: Configuration values not being set

#### Solution:

```php

// Ensure you're using the same ConfigHandler instance
$config = new ConfigHandler();

// Set values before retrieving
$config->set('app.name', 'My Application');
$config->set('app.port', 8080);

// Now you can retrieve them
$name = $config->get('app.name');
$port = $config->get('app.port');
    
```

## ✅ Validation Issues

### Problem: "Configuration validation failed" errors

```php

// Error: [CONFIG_200] Configuration validation failed for key "app.port"
    
```

#### Solution:

```php

use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Exceptions\ConfigValidationException;

// 1. Ensure validators are registered
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());

// 2. Check validation rules format
$config->setValidationRules([
    'app.port' => ['type:int', 'range:1-65535'], // Correct rule parsing format
    'app.name' => ['required'] // Simple rule format
]);

// 3. Set values with correct types and handle exceptions
try {
    $config->set('app.port', 8080); // Integer, not string
    $config->set('app.name', 'My App');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    echo "Validation failed for: " . $context['configKey'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    
    // Fix the issue based on context
    if (isset($context['expectedType'])) {
        echo "Expected type: " . $context['expectedType'] . "\n";
    }
}
    
```

### Problem: Custom validators not working

#### Solution:

```php

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class CustomValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        // Support both simple and parameterized rules
        $ruleName = str_contains($rule, ':') ? explode(':', $rule)[0] : $rule;
        return $ruleName === 'custom';
    }
    
    public function validate(mixed $value, array $context = []): void {
        if (!$this->isValid($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Custom validation failed',
                    'rule' => 'custom',
                    'value' => $value
                ])
            );
        }
    }
}

// Register the validator BEFORE setting validation rules
$config->registerValidator(new CustomValidator());
$config->setValidationRules(['api.key' => ['custom']]);
    
```

## 🔄 Service Container Issues

### Problem: "Service not found" error

```php

// Error: [SERVICE_100] Service "unknown_service" was not found in the container
    
```

#### Solution:

```php

use Dino\Exceptions\ServiceNotFoundException;

// 1. Register services before using them
$container->addFactory('logger', new LoggerFactory());
$container->singleton('cache', fn() => new CacheService());

// 2. Check if service exists before getting
if ($container->has('logger')) {
    $logger = $container->get('logger');
} else {
    // Use fallback or throw meaningful error
    $logger = new NullLogger();
}

// 3. Handle exception gracefully
try {
    $service = $container->get('unknown_service');
} catch (ServiceNotFoundException $e) {
    $context = $e->getContext();
    $availableServices = $context['available_services'] ?? [];
    
    echo "Available services: " . implode(', ', $availableServices) . "\n";
    echo "Please register the service first using addFactory() or singleton()\n";
}
    
```

### Problem: Lazy loading not working

#### Solution:

```php

use Dino\Core\LazyServiceWrapper;

// Register as lazy service (third parameter = true)
$container->singleton('heavyService', function() {
    return new HeavyService(); // Expensive initialization
}, true); // Enable lazy loading

// Service is not initialized yet
$lazyService = $container->get('heavyService');

// Initialization happens on first method call
$result = $lazyService->processData();
    
```

## 🎯 Dependency Injection Issues

### Problem: "Circular dependency detected" error

```php

// Error: [SERVICE_101] Circular dependency detected for service "ServiceA"
    
```

#### Solution:

```php

use Dino\Exceptions\CircularDependencyException;

try {
    $service = $resolver->resolve(ServiceA::class);
} catch (CircularDependencyException $e) {
    $context = $e->getContext();
    $dependencyChain = $context['dependency_chain'] ?? [];
    
    echo "Circular dependency chain:\n";
    foreach ($dependencyChain as $serviceName) {
        echo " → " . $serviceName . "\n";
    }
    
    // Fix: Use lazy loading or refactor dependencies
    $container->singleton('ServiceA', fn() => new ServiceA(), true);
    $container->singleton('ServiceB', fn() => new ServiceB(), true);
}
    
```

### Problem: "Unresolvable parameter" error

```php

// Error: [DI_300] Cannot resolve parameter "unknownService"
    
```

#### Solution:

```php

use Dino\Exceptions\UnresolvableParameterException;

class MyService {
    public function __construct(
        private LoggerInterface $logger, // Needs interface binding
        private ?ConfigHandler $config = null // Optional parameter
    ) {}
}

try {
    $service = $resolver->resolve(MyService::class);
} catch (UnresolvableParameterException $e) {
    $context = $e->getContext();
    
    echo "Parameter: " . $context['parameter'] . "\n";
    echo "Type: " . $context['type'] . "\n";
    
    // Fix: Bind interface to implementation
    $container->bind(LoggerInterface::class, FileLogger::class);
    
    // Or make parameter optional with default value
}
    
```

## 🚨 Error Handling Issues

### Problem: Error messages not descriptive enough

#### Solution:

```php

use Dino\Core\ErrorMessageFormatter;
use Dino\Exceptions\ConfigValidationException;

try {
    $config->set('app.port', 'invalid');
} catch (ConfigValidationException $e) {
    // Use ErrorMessageFormatter for consistent formatting
    $formattedMessage = ErrorMessageFormatter::format($e);
    echo $formattedMessage . "\n";
    
    // Get detailed error information
    $errorDetails = ErrorMessageFormatter::createDetails($e);
    echo "Error Details:\n";
    echo "Code: " . $errorDetails['code'] . "\n";
    echo "Severity: " . $errorDetails['severity'] . "\n";
    echo "Context: " . json_encode($errorDetails['context'], JSON_PRETTY_PRINT) . "\n";
}
    
```

### Problem: Custom exceptions not showing context

#### Solution:

```php

use Dino\Exceptions\ContextAwareException;

class CustomException extends ContextAwareException {
    public function __construct(string $customData, array $context = []) {
        parent::__construct(
            'CUSTOM_001', // Your error code
            "Custom error occurred: {$customData}",
            array_merge($context, ['custom_data' => $customData]),
            'MEDIUM' // Severity level
        );
    }
}

// Usage
throw new CustomException('Invalid operation', [
    'user_id' => 123,
    'operation' => 'update'
]);
    
```

## Debugging Tips

### Enable Detailed Logging

```php

// Log all configuration operations
$config->set('debug.mode', true);

// Use context information for debugging
try {
    // Your code here
} catch (ContextAwareException $e) {
    error_log("Error: " . $e->getErrorMessage());
    error_log("Context: " . json_encode($e->getContext(), JSON_PRETTY_PRINT));
    error_log("Stack trace: " . $e->getTraceAsString());
}
    
```

### Check Validator Registry

```php

// Debug which validators are registered
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());

// In your debugging code:
echo "Validators registered successfully\n";
// Check if specific rule is supported
if ($config->supportsRule('type:int')) {
    echo "type:int rule is supported\n";
}
    
```

## Common Mistakes to Avoid

*   ❌ Forgetting to register validators before setting validation rules
*   ❌ Using wrong rule format (use `type:int` not `type int`)
*   ❌ Not handling context-aware exceptions specifically
*   ❌ Missing interface bindings for dependency injection
*   ❌ Not using lazy loading for expensive services
*   ❌ Ignoring error context information in exception handling

## Getting Help

If you're still experiencing issues:

1.  Check the [API Reference](../API-Reference/index.html) for detailed method documentation
2.  Review the [Error Handling Guide](../Guides/error-handling.html) for advanced patterns
3.  Examine the [Example files](../Examples/) for working code samples
4.  Enable debug mode and check the full error context for clues

## Related Resources

*   [Error Handling Guide](../Guides/error-handling.html)
*   [Validation System Guide](../Guides/validation-system.html)
*   [Configuration Management Guide](../Guides/config-management.html)
*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.html)
*   [ValidatorInterface API Reference](../API-Reference/ValidatorInterface.html)