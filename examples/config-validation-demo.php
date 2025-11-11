<?php

declare(strict_types=1);

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;
use Dino\Exceptions\ValidationException;

require __DIR__ . '/../vendor/autoload.php';

$config = new ConfigHandler();

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

// Define validation rules
$config->setValidationRules([
    'app.name'  => ['required', 'type:string'],
    'app.port'  => ['required', 'type:int', 'range'],
    'app.email' => ['required', 'regex'],
]);

echo "=== Dino Config Validation Demo ===\n";

try {
    // Valid configuration
    $config->set('app.name', 'Dino Library');
    $config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
    $config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);

    echo "✅ All valid configuration values accepted.\n";

    // Invalid configuration (out of range)
    $config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
} catch (ValidationException $e) {
    echo "❌ Validation failed: " . $e->getMessage() . "\n";
}
