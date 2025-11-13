ValidatorInterface API Reference - Dino Library v1.2.1 

# ValidatorInterface API Reference

The `ValidatorInterface` defines the contract for all validation rules in Dino Library. Validators are responsible for validating configuration values and other data with rich error context and rule parsing support.

## Interface Synopsis

```php

namespace Dino\Contracts\Validation;

interface ValidatorInterface {
    // Core validation methods
    public function supports(string $rule): bool;
    public function validate(mixed $value, array $context = []): void;
}
    
```

## Methods

### supports(string $rule): bool

Checks if the validator supports the given rule. Works with both simple rule names and rule strings with parameters.

#### Parameters

*   `rule` (string) - The rule to check support for

#### Returns

bool - True if the validator supports the rule, false otherwise

#### Rule Parsing Support

Validators should support both simple rule names and rule strings with parameters:

*   `required` → simple rule name
*   `type:int` → rule with parameter (extracts 'type')
*   `range:1-100` → rule with parameters (extracts 'range')

#### Examples

```php

class TypeValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        $ruleName = str_contains($rule, ':') ? explode(':', $rule)[0] : $rule;
        return $ruleName === 'type';
    }
}

$validator = new TypeValidator();
$validator->supports('type'); // true
$validator->supports('type:int'); // true
$validator->supports('required'); // false
    
```

### validate(mixed $value, array $context = \[\]): void

Validates the given value against the rule with rich context information for error reporting.

#### Parameters

*   `value` (mixed) - The value to validate
*   `context` (array) - Context information including rule parameters, config key, and validation metadata

#### Exceptions

*   `ConfigValidationException` - If validation fails

#### Context Parameters

The context array provides rich information for validation and error reporting:

*   `configKey` (string) - The configuration key being validated
*   `rule` (string) - The rule being applied
*   `expectedType` (string) - For type validation
*   `min` (int) - For range validation (minimum value)
*   `max` (int) - For range validation (maximum value)
*   `pattern` (string) - For regex validation
*   `source` (string) - Source of the configuration value

#### Examples

```php

class RangeValidator implements ValidatorInterface {
    public function validate(mixed $value, array $context = []): void {
        $min = $context['min'] ?? null;
        $max = $context['max'] ?? null;
        
        if ($min === null || $max === null) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Range validation requires min and max parameters',
                    'rule' => 'range'
                ])
            );
        }
        
        if (!is_numeric($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Value must be numeric for range validation',
                    'rule' => 'range',
                    'value' => $value
                ])
            );
        }
        
        $numericValue = (float)$value;
        if ($numericValue < $min || $numericValue > $max) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Value must be between {$min} and {$max}",
                    'rule' => 'range',
                    'value' => $value,
                    'min' => $min,
                    'max' => $max,
                    'actual' => $numericValue
                ])
            );
        }
    }
}
    
```

## Built-in Validators

### RequiredValidator

Validates that a value is not empty, null, or an empty string.

```php

$config->setValidationRules(['api.key' => ['required']]);
    
```

### TypeValidator

Validates that a value matches the expected type with detailed type mapping.

```php

$config->setValidationRules([
    'app.port' => ['type:int'],
    'app.debug' => ['type:bool'],
    'app.name' => ['type:string']
]);
    
```

### RangeValidator

Validates that a numeric value falls within the specified range.

```php

$config->setValidationRules([
    'app.port' => ['range:1-65535'],
    'user.age' => ['range:18-120']
]);
    
```

### RegexValidator

Validates that a value matches the specified regular expression pattern.

```php

$config->setValidationRules([
    'app.email' => ['regex:/^[^@]+@[^@]+\.[^@]+$/'],
    'app.version' => ['regex:/^\d+\.\d+\.\d+$/']
]);
    
```

## Creating Custom Validators

### Basic Custom Validator

```php

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class EmailValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        return $rule === 'email';
    }
    
    public function validate(mixed $value, array $context = []): void {
        if (!is_string($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Email must be a string',
                    'rule' => 'email',
                    'actualType' => gettype($value)
                ])
            );
        }
        
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Invalid email format',
                    'rule' => 'email',
                    'value' => $value
                ])
            );
        }
    }
}

// Register and use
$config->registerValidator(new EmailValidator());
$config->setValidationRules(['contact.email' => ['email']]);
    
```

### Advanced Custom Validator with Parameters

```php

class LengthValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        return str_starts_with($rule, 'length');
    }
    
    public function validate(mixed $value, array $context = []): void {
        // Extract parameters from rule or context
        $minLength = $context['min'] ?? null;
        $maxLength = $context['max'] ?? null;
        
        if (!is_string($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Length validation requires string value',
                    'rule' => 'length'
                ])
            );
        }
        
        $length = strlen($value);
        
        if ($minLength !== null && $length < $minLength) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Value must be at least {$minLength} characters long",
                    'rule' => 'length',
                    'minLength' => $minLength,
                    'actualLength' => $length
                ])
            );
        }
        
        if ($maxLength !== null && $length > $maxLength) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => "Value must be at most {$maxLength} characters long",
                    'rule' => 'length',
                    'maxLength' => $maxLength,
                    'actualLength' => $length
                ])
            );
        }
    }
}
    
```

## Error Handling Best Practices

### Rich Error Context

```php

try {
    $config->set('app.port', 'invalid_value');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    
    // Use context for detailed error reporting
    echo "Validation failed for: " . $context['configKey'] . "\n";
    echo "Rule: " . $context['rule'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    
    if (isset($context['expectedType'])) {
        echo "Expected: " . $context['expectedType'] . "\n";
    }
    if (isset($context['actualType'])) {
        echo "Actual: " . $context['actualType'] . "\n";
    }
}
    
```

### Custom Error Messages

```php

class CustomValidator implements ValidatorInterface {
    public function validate(mixed $value, array $context = []): void {
        if (!$this->isValid($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => $this->getCustomErrorMessage($value, $context),
                    'rule' => 'custom',
                    'value' => $value,
                    'validationDetails' => $this->getValidationDetails($value)
                ])
            );
        }
    }
    
    private function getCustomErrorMessage($value, $context): string {
        return sprintf(
            "Custom validation failed for '%s' with value '%s'",
            $context['configKey'] ?? 'unknown',
            is_scalar($value) ? $value : gettype($value)
        );
    }
}
    
```

## Integration with ConfigHandler

### Complete Setup Example

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;

$config = new ConfigHandler();

// Register built-in validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());

// Register custom validator
$config->registerValidator(new EmailValidator());

// Set validation rules
$config->setValidationRules([
    'app.name' => ['required'],
    'app.port' => ['type:int', 'range:1-65535'],
    'contact.email' => ['email']
]);

// Usage with automatic validation
$config->set('app.name', 'My Application');
$config->set('app.port', 8080);
$config->set('contact.email', 'user@example.com');
    
```

## Best Practices

*   Implement `supports()` to handle both simple rules and rule strings with parameters
*   Use rich context information for detailed error reporting
*   Provide clear and descriptive error messages in the context
*   Validate parameter presence and types in custom validators
*   Support both rule parsing and context parameters for flexibility
*   Include relevant validation metadata in error context

## See Also

*   [ConfigHandler API Reference](ConfigHandler.html)
*   [ConfigValidationException API Reference](ConfigValidationException.html)
*   [Validation System Guide](../Guides/validation-system.html)
*   [Configuration Management Guide](../Guides/config-management.html)