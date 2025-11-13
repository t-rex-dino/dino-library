Advanced Validation System - Dino Library v1.2.1

# Advanced Validation System

Comprehensive guide for the enhanced validation system with rule parsing and context-aware validation.

## Overview

The validation system in Dino Library v1.2.1 provides a flexible, extensible framework for validating configuration values and other data with rich error reporting.

## Core Components

### ValidatorRegistry

Central registry for managing all validators with support for dynamic rule parsing.

```php

$registry = new ValidatorRegistry();
$registry->register(new RequiredValidator());
$registry->register(new TypeValidator());
    
```

### Rule Parsing System

Intelligent rule parsing with parameter extraction for complex validation scenarios.

```php

// Rule parsing examples:
// "type:int" → ['rule' => 'type', 'expectedType' => 'int']
// "range:1-100" → ['rule' => 'range', 'min' => 1, 'max' => 100]
// "regex:/^[a-z]+$/" → ['rule' => 'regex', 'pattern' => '/^[a-z]+$/']
    
```

## Integration with ConfigHandler

### Basic Setup

```php

$config = new ConfigHandler();

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

// Define validation rules
$config->setValidationRules([
    'app.name' => ['required'],
    'app.port' => ['type:int', 'range:1-65535'],
    'app.email' => ['regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
]);
    
```

### Context-Aware Validation

Validators receive rich context information for detailed error reporting.

```php

try {
    $config->set('app.port', 'invalid_string');
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    // Context contains: configKey, rule, expectedType, actualType, value, etc.
    echo $context['reason']; // "Expected type 'int', got 'string'"
}
    
```

## Available Validators

### RequiredValidator

Ensures values are not empty, null, or empty strings.

```php

$config->setValidationRules(['api.key' => ['required']]);
    
```

### TypeValidator

Validates value types with detailed type mapping.

```php

$config->setValidationRules([
    'app.port' => ['type:int'],
    'app.debug' => ['type:bool'],
    'app.name' => ['type:string']
]);
    
```

### RangeValidator

Validates numeric ranges with inclusive bounds.

```php

$config->setValidationRules([
    'app.port' => ['range:1-65535'],
    'user.age' => ['range:18-120']
]);
    
```

### RegexValidator

Validates values against regular expression patterns.

```php

$config->setValidationRules([
    'app.email' => ['regex:/^[^@]+@[^@]+\.[^@]+$/'],
    'app.version' => ['regex:/^\d+\.\d+\.\d+$/']
]);
    
```

## Error Handling & Reporting

### Rich Error Context

```php

try {
    $config->set('app.port', 70000); // Out of range
} catch (ConfigValidationException $e) {
    $context = $e->getContext();
    echo "Config Key: " . $context['configKey'] . "\n";
    echo "Rule: " . $context['rule'] . "\n";
    echo "Reason: " . $context['reason'] . "\n";
    echo "Value: " . $context['value'] . "\n";
    echo "Expected Range: " . $context['min'] . "-" . $context['max'] . "\n";
}
    
```

### Custom Validators

Extend the system by implementing the ValidatorInterface.

```php

class CustomValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        return $rule === 'custom';
    }
    
    public function validate(mixed $value, array $context = []): void {
        // Custom validation logic
        if (!$this->isValidCustom($value)) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Custom validation failed',
                    'rule' => 'custom'
                ])
            );
        }
    }
}
    
```

## Best Practices

*   Define validation rules close to configuration definition
*   Use specific error messages with rich context
*   Register validators early in application bootstrap
*   Combine multiple validators for complex validation scenarios