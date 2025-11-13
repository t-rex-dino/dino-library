 Advanced Validation Demo - Dino Library v1.2.1  body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; } h1, h2, h3 { color: #2c3e50; } pre { background: #f7f9fa; padding: 12px; border-left: 4px solid #3498db; overflow-x: auto; } code { font-family: Consolas, monospace; }

# Advanced Validation Demo

This demo illustrates how Dino Library v1.2.1 integrates multiple validators with `ValidatorRegistry` and `ConfigHandler`. Each rule throws context-aware exceptions when validation fails.

## 1) Required Rule

```php
<?php
use Dino\Core\Validation\ValidatorRegistry;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

$registry = new ValidatorRegistry();
$registry->register(new RequiredValidator());

try {
    $registry->validate('required', '', ['configKey' => 'app.name']);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
```

### Expected Output

```
[CONFIG_200] Validation failed for config key "app.name".
Context: {"rule":"required","reason":"Value is required and cannot be empty"}
```

## 2) Type Rule

```php
<?php
use Dino\Validation\Rules\TypeValidator;

$registry->register(new TypeValidator());

try {
    $registry->validate('type:int', 'abc', ['configKey' => 'db.port']);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
```

### Expected Output

```
[CONFIG_200] Validation failed for config key "db.port".
Context: {"rule":"type:int","reason":"Expected type 'int', got 'string'"}
```

## 3) Range Rule

```php
<?php
use Dino\Validation\Rules\RangeValidator;

$registry->register(new RangeValidator());

try {
    $registry->validate('range:1-10', 42, ['configKey' => 'app.level']);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
```

### Expected Output

```
[CONFIG_200] Validation failed for config key "app.level".
Context: {"rule":"range:1-10","reason":"Value 42 is outside the allowed range [1,10]"}
```

## 4) Regex Rule

```php
<?php
use Dino\Validation\Rules\RegexValidator;

$registry->register(new RegexValidator());

try {
    $registry->validate('regex:/^[a-z]+$/', 'ABC123', ['configKey' => 'app.slug']);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
```

### Expected Output

```
[CONFIG_200] Validation failed for config key "app.slug".
Context: {"rule":"regex:/^[a-z]+$/","reason":"Value does not match the required regex pattern"}
```

## Best Practices

*   Register all validators in `ValidatorRegistry` before use.
*   Always provide `configKey` and `rule` in context for actionable error messages.
*   Catch `ConfigValidationException` and format with `ErrorMessageFormatter` for consistent output.
*   Use structured logging to capture `errorCode`, `message`, and `context`.