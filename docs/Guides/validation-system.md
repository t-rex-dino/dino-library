# 🔒 Validation System

Dino Library provides a robust validation system to ensure configuration integrity and data consistency.

**Tip:** All validators throw `ValidationException` with detailed error messages.

## 📋 Available Validators

| Validator | Description | Required Context |
| --- | --- | --- |
| **RequiredValidator** | Ensures value is not empty, null, or empty string | None |
| **TypeValidator** | Validates data type (string, int, bool, float, array, object) | None (infers from rule) |
| **RangeValidator** | Checks numeric ranges | `min`, `max` |
| **RegexValidator** | Validates values against a regex pattern | `pattern` |
| **EmailValidator** | Validates email addresses format | None |

## 🚀 Usage Examples

### Basic Configuration Validation

```php

use DinoLibrary\ConfigHandler;

$config = new ConfigHandler();

// Define validation rules
$config->setValidationRules([
    'app.name'      => ['required', 'type:string'],
    'app.port'      => ['required', 'type:int', 'range'],
    'app.debug'     => ['type:bool'],
    'app.email'     => ['required', 'email'],
    'app.version'   => ['required', 'regex']
]);

// Set values with validation
$config->set('app.name', 'My Application');
$config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
$config->set('app.email', 'admin@example.com');
$config->set('app.version', '1.2.0', ['pattern' => '/^\d+\.\d+\.\d+$/']);

echo $config->get('app.name'); // "My Application"
```

### Handling Validation Errors

```php

try {
    $config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
} catch (DinoLibrary\ValidationException $e) {
    echo "Validation failed: " . $e->getMessage();
    // Output: Validation failed: Value 70000 is out of range. Must be between 1 and 65535.
}
```

## 🛠 Creating Custom Validators

You can create custom validators by implementing the `ValidatorInterface`:

```php

use DinoLibrary\ValidatorInterface;
use DinoLibrary\ValidationException;

class UrlValidator implements ValidatorInterface {
    public function supports(string $rule): bool {
        return $rule === 'url';
    }
    
    public function validate(mixed $value, array $context = []): void {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new ValidationException(
                "Invalid URL format: " . (string)$value,
                $context
            );
        }
    }
}

// Register custom validator
$config->registerValidator(new UrlValidator());

// Use in validation rules
$config->setValidationRules([
    'website.url' => ['required', 'url']
]);
```

## 🎯 Best Practices

*   **Always validate critical configuration** - Prevent runtime errors with proper validation
*   **Use context parameters** - Provide `min`/`max` for ranges and `pattern` for regex
*   **Create domain-specific validators** - Build custom validators for your business logic
*   **Validate early** - Set validation rules before setting values
*   **Handle exceptions gracefully** - Catch `ValidationException` for better user experience

## 🔧 Integration with Service Container

Validators can be registered through the service container for better dependency management:

```php

$container = new ServiceContainer();
$container->register('validator.email', function() {
    return new EmailValidator();
});

$config->registerValidator($container->get('validator.email'));
```

- - -

**Next:** Learn about [ConfigHandler API](../API-Reference/ConfigHandler.md) or check [configuration examples](../Examples/config-handler-demo-explained.md).