Error Handling Demo - Dino Library v1.2.1

# Error Handling Demo (Context-Aware Exceptions)

This demo shows how to catch, format, and log context-aware exceptions in Dino Library v1.2.1. It covers configuration validation, missing keys, and service resolution failures.

## Prerequisites

*   PHP 8.1+
*   Dino Library v1.2.1
*   Autoload via Composer

**Tip:** Always include `configKey`, `rule`, and `reason` in the validation context for actionable error messages.

## 1) Validation error with ConfigValidationException

Validating an integer port using the TypeValidator through ValidatorRegistry and formatting the error.

```php
<?php

use Dino\Core\Validation\ValidatorRegistry;
use Dino\Validation\Rules\TypeValidator;
use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

require __DIR__ . '/../vendor/autoload.php';

$registry = new ValidatorRegistry();
$registry->register(new TypeValidator());

$configKey = 'db.port';
$value = 'not-an-int';
$rule = 'type:int';

try {
    $registry->validate($rule, $value, [
        'configKey' => $configKey,
        'rule' => $rule
    ]);
    echo "Validation passed for {$configKey}" . PHP_EOL;
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e) . PHP_EOL;
    // Access raw context when needed
    $ctx = $e->getContext();
    // Example: structured logging
    error_log(json_encode([
        'errorCode' => $e->getCode(),
        'message' => $e->getMessage(),
        'context' => $ctx
    ], JSON_THROW_ON_ERROR));
}
```

### Expected output

```
[CONFIG_200] Validation failed for config key "db.port".
Context: {"rule":"type:int","reason":"Expected type 'int', got 'string'"}
```

## 2) Missing configuration key with ConfigNotFoundException

Fetching a non-existent configuration key should raise a context-rich exception.

```php
<?php

use Dino\Core\Config\ConfigHandler;
use Dino\Exceptions\ConfigNotFoundException;
use Dino\Core\ErrorMessageFormatter;

require __DIR__ . '/../vendor/autoload.php';

$handler = new ConfigHandler([
    'app.name' => 'Dino',
    // 'app.port' intentionally missing
]);

try {
    $port = $handler->get('app.port');
    echo "App port: {$port}" . PHP_EOL;
} catch (ConfigNotFoundException $e) {
    echo ErrorMessageFormatter::format($e) . PHP_EOL;
}
```

### Expected output

```
[CONFIG_404] Configuration key 'app.port' not found.
Context: {"configKey":"app.port"}
```

## 3) Service resolution failure with ServiceResolutionException

Resolving a service that has unresolvable dependencies should produce a clear, actionable error.

```php
<?php

use Dino\Core\Container\ServiceContainer;
use Dino\Exceptions\ServiceResolutionException;
use Dino\Core\ErrorMessageFormatter;

require __DIR__ . '/../vendor/autoload.php';

$container = new ServiceContainer();

// Example: Register a factory that depends on a missing service
$container->factory('mailer', function ($c) {
    // Suppose 'smtpClient' is not registered and cannot be auto-resolved
    $smtp = $c->get('smtpClient');
    return new Mailer($smtp);
});

try {
    $mailer = $container->get('mailer');
    echo "Mailer resolved." . PHP_EOL;
} catch (ServiceResolutionException $e) {
    echo ErrorMessageFormatter::format($e) . PHP_EOL;
}
```

### Expected output

```
[SERVICE_500] Failed to resolve service 'mailer'.
Context: {"reason":"Dependency 'smtpClient' not found or unresolvable"}
```

## 4) Validator not found with ValidatorNotFoundException

Calling validation for an unsupported rule should raise a dedicated exception.

```php
<?php

use Dino\Core\Validation\ValidatorRegistry;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Exceptions\ValidatorNotFoundException;
use Dino\Core\ErrorMessageFormatter;

require __DIR__ . '/../vendor/autoload.php';

$registry = new ValidatorRegistry();
$registry->register(new RequiredValidator());

try {
    // 'regex' rule not registered in this example
    $registry->validate('regex:/^[a-z]+$/', 'ABC', [
        'configKey' => 'app.slug'
    ]);
} catch (ValidatorNotFoundException $e) {
    echo ErrorMessageFormatter::format($e) . PHP_EOL;
}
```

### Expected output

```
[VALIDATION_400] No validator supports rule 'regex:/^[a-z]+$/'.
Context: {"rule":"regex:/^[a-z]+$/"}
```

## Logging and observability

Use structured logging to capture error code, message, and full context for better observability.

```php
<?php

use Psr\Log\LoggerInterface;
use Dino\Exceptions\ContextAwareException;

/** @var LoggerInterface $logger */
function logException(ContextAwareException $e, LoggerInterface $logger): void {
    $logger->error($e->getMessage(), [
        'errorCode' => $e->getCode(),
        'context' => $e->getContext(),
        'exception' => get_class($e)
    ]);
}
```

## Best practices

*   Prefer catching specific exceptions (`ConfigValidationException`, `ServiceNotFoundException`, etc.) over generic ones.
*   Populate `context` with actionable fields like `configKey`, `rule`, and a human-readable `reason`.
*   Use `ErrorMessageFormatter` for consistent human-friendly outputs, and structured logging for machine analysis.
*   Keep validators lightweight and deterministic; avoid side effects inside `validate()`.