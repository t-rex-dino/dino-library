# Configuration Validation Explained

This example demonstrates how to use the Dino Library validation system to ensure configuration integrity.

## Setup

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;

$config = new ConfigHandler();

$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

$config->setValidationRules([
    'app.name'  => ['required', 'type:string'],
    'app.port'  => ['required', 'type:int', 'range'],
    'app.email' => ['required', 'regex']
]);
```

## Valid Configuration

```php

$config->set('app.name', 'Dino Library');
$config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
$config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);
```

## Invalid Configuration

```php

try {
    $config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
} catch (ValidationException $e) {
    echo "❌ Validation failed: " . $e->getMessage();
}
```

## Output

```

✅ All valid configuration values accepted.
❌ Validation failed: Configuration key 'app.port' must be between 1 and 65535, 70000 given
```

## Key Takeaways

*   Validation rules ensure configuration integrity
*   Validators are modular and extensible
*   Error messages provide clear context for debugging