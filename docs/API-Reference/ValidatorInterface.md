# ValidatorInterface

The `ValidatorInterface` defines the contract for all validators used in the Dino Library validation system. Each validator must declare which rules it supports and implement the validation logic.

## Interface Definition

```php

namespace Dino\Contracts\Validation;

interface ValidatorInterface
{
    public function supports(string $rule): bool;
    public function validate(mixed $value, array $context = []): void;
}
```

## Usage

```php

$config->registerValidator(new TypeValidator());
$config->setValidationRules([
    'app.port' => ['type:int', 'range']
]);
```

## Best Practices

*   Keep validators modular and reusable
*   Use `supports()` to clearly declare rule compatibility
*   Throw `ValidationException` with context for better debugging