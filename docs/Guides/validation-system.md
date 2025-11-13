Validation System Guide - Dino Library v1.2.1 

# 🔒 Advanced Validation System

Dino Library v1.2.1 provides a robust and extensible validation system to ensure configuration integrity and data consistency with advanced rule parsing and context-aware error reporting.

**Tip:** All validators throw `ConfigValidationException` with rich context information for detailed debugging.

## 📋 Available Validators

| Validator | Description | Rule Examples | Context Parameters |
| --- | --- | --- | --- |
| **RequiredValidator** | Ensures value is not empty, null, or empty string | `required` | None |
| **TypeValidator** | Validates data types with intelligent type mapping | `type:int`, `type:bool`, `type:string` | `expectedType` (auto-extracted from rule) |
| **RangeValidator** | Checks numeric ranges with inclusive bounds | `range:1-100`, `range:0-999` | `min`, `max` (auto-extracted from rule) |
| **RegexValidator** | Validates values against regular expression patterns | `regex:/^[a-z]+$/`, `regex:/^\d+\.\d+$/` | `pattern` (auto-extracted from rule) |

## 🚀 Advanced Usage Examples

### Basic Configuration Validation with Rule Parsing

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;
use Dino\Exceptions\ConfigValidationException;

$config = new ConfigHandler();

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

// Define validation rules with intelligent rule parsing
$config->setValidationRules([
    'app.name'      => ['required'],
    'app.port'      => ['type:int', 'range:1-65535'], // Rule parsing: extracts type and range
    'app.debug'     => ['type:bool'],
    'app.email'     => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
    'app.version'   => ['required', 'regex:/^\d+\.\d+\.\d+$/']
]);

// Set values - validation happens automatically
$config->set('app.name', 'My Application');
$config->set('app.port', 8080); // No context needed - rule parsing handles it!
$config->set('app.debug', true);
$config->set('app.email', 'admin@example.com');
$config->set('app.version', '1.2.1');

echo $config->get('app.name'); // "My Application"
    
```

### Advanced Error Handling with Rich Context

```php

try {
    $config->set('app.port', 70000); // Out of range
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    echo "Error Code: " . $e->getErrorCode() . "\n";
    echo "Config Key: " . $context['configKey'] . "\n";
    echo "Failed Rules: " . implode(', ', $context['rules']) . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    echo "Value: " . $context['value'] . "\n";
    echo "Expected Range: " . $context['min'] . "-" . $context['max'] . "\n";
    echo "Actual Value: " . $context['actual'] . "\n";
    
    // Output:
    // Error Code: CONFIG_200
    // Config Key: app.port
    // Failed Rules: range:1-65535
    // Reason: Value must be between 1 and 65535
    // Value: 70000
    // Expected Range: 1-65535
    // Actual Value: 70000
}

try {
    $config->set('app.debug', 'not_a_boolean');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    echo "Type validation failed: Expected " . $context['expectedType'] . ", got " . $context['actualType'] . "\n";
}
    
```

## 🛠 Creating Advanced Custom Validators

### Custom Validator with Rule Parsing Support

```php

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class UrlValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        // Support both simple 'url' and parameterized 'url:https' rules
        $ruleName = str_contains($rule, ':') ? explode(':', $rule)[0] : $rule;
        return $ruleName === 'url';
    }
    
    public function validate(mixed $value, array $context = []): void {
        $scheme = $this->extractScheme($context);
        
        if (!is_string($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'URL must be a string',
                    'rule' => 'url',
                    'actualType' => gettype($value),
                    'value' => $value
                ])
            );
        }
        
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Invalid URL format: {$value}",
                    'rule' => 'url',
                    'value' => $value,
                    'requiredScheme' => $scheme
                ])
            );
        }
        
        // Validate specific scheme if required
        if ($scheme && !str_starts_with($value, $scheme . '://')) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "URL must use {$scheme} scheme",
                    'rule' => 'url',
                    'value' => $value,
                    'requiredScheme' => $scheme,
                    'actualScheme' => parse_url($value, PHP_URL_SCHEME)
                ])
            );
        }
    }
    
    private function extractScheme(array $context): ?string {
        // Extract from rule parameter (url:https) or context
        $rule = $context['rule'] ?? '';
        if (str_contains($rule, ':')) {
            return explode(':', $rule)[1];
        }
        return $context['scheme'] ?? null;
    }
}

// Register and use custom validator
$config->registerValidator(new UrlValidator());

// Usage with rule parsing
$config->setValidationRules([
    'website.url' => ['required', 'url:https'], // Requires HTTPS
    'api.endpoint' => ['required', 'url'] // Any URL scheme
]);
    
```

### Domain-Specific Custom Validator

```php

class PriceValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        return $rule === 'price';
    }
    
    public function validate(mixed $value, array $context = []): void {
        $min = $context['min'] ?? 0;
        $max = $context['max'] ?? PHP_FLOAT_MAX;
        $currency = $context['currency'] ?? 'USD';
        
        if (!is_numeric($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Price must be numeric',
                    'rule' => 'price',
                    'actualType' => gettype($value)
                ])
            );
        }
        
        $price = (float)$value;
        
        if ($price < $min) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Price must be at least {$min} {$currency}",
                    'rule' => 'price',
                    'minPrice' => $min,
                    'actualPrice' => $price,
                    'currency' => $currency
                ])
            );
        }
        
        if ($price > $max) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Price must not exceed {$max} {$currency}",
                    'rule' => 'price',
                    'maxPrice' => $max,
                    'actualPrice' => $price,
                    'currency' => $currency
                ])
            );
        }
    }
}
    
```

## 🎯 Best Practices

*   **Leverage rule parsing** - Use parameterized rules (`type:int`, `range:1-100`) for cleaner code
*   **Validate critical configuration early** - Prevent runtime errors with proper validation during bootstrap
*   **Use rich context information** - Provide detailed error context for better debugging and user experience
*   **Create domain-specific validators** - Build custom validators tailored to your business logic
*   **Handle exceptions gracefully** - Catch `ConfigValidationException` and use context for meaningful error messages
*   **Combine multiple validators** - Use multiple rules for complex validation scenarios
*   **Register validators early** - Set up validation before setting configuration values

## 🔧 Integration Patterns

### With Service Container

```php

use Dino\Core\ServiceContainer;

$container = new ServiceContainer();

// Register validators as services
$container->singleton('validator.required', fn() => new RequiredValidator());
$container->singleton('validator.type', fn() => new TypeValidator());
$container->singleton('validator.range', fn() => new RangeValidator());
$container->singleton('validator.custom.url', fn() => new UrlValidator());

// Register all validators with ConfigHandler
$config = new ConfigHandler();
$config->registerValidator($container->get('validator.required'));
$config->registerValidator($container->get('validator.type'));
$config->registerValidator($container->get('validator.range'));
$config->registerValidator($container->get('validator.custom.url'));
    
```

### With Dependency Injection

```php

use Dino\Core\DependencyResolver;

class ApplicationSetup {
    public function __construct(
        private ConfigHandler $config,
        private DependencyResolver $resolver
    ) {}
    
    public function initialize(): void {
        // Auto-wire and register all validators
        $validators = [
            RequiredValidator::class,
            TypeValidator::class,
            RangeValidator::class,
            UrlValidator::class
        ];
        
        foreach ($validators as $validatorClass) {
            $validator = $this->resolver->resolve($validatorClass);
            $this->config->registerValidator($validator);
        }
        
        // Set validation rules
        $this->config->setValidationRules([
            'app.name' => ['required'],
            'app.port' => ['type:int', 'range:1-65535']
        ]);
    }
}
    
```

## 📚 Related Documentation

*   [ConfigHandler API Reference](../API-Reference/ConfigHandler.html)
*   [ValidatorInterface API Reference](../API-Reference/ValidatorInterface.html)
*   [Configuration Management Guide](config-management.html)
*   [Error Handling Guide](error-handling.html)
*   [Advanced Validation Demo](../Examples/advanced-validation-demo.html)