# Config Handlers Guide

The `ConfigHandler` provides a flexible way to manage configuration values with support for dot notation, hierarchical structures, and validation rules.

## Basic Usage

```php

$config = new ConfigHandler();
$config->set('app.name', 'Dino Library');
$config->set('database.connections.mysql.host', 'localhost');
```

## Loading Configuration

```php

$config->loadFromFile('config/app.php');
```

## Configuration Validation (New in v1.1.1)

Starting from version **1.1.1**, the `ConfigHandler` supports validation rules to ensure configuration integrity.

### Defining Validation Rules

```php

$config->setValidationRules([
    'app.name'  => ['required', 'type:string'],
    'app.port'  => ['required', 'type:int', 'range'],
    'app.email' => ['required', 'regex']
]);
```

### Registering Validators

```php

$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());
```

### Validation in Action

```php

try {
    $config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
    $config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);
    echo "✅ Configuration values are valid.";
} catch (ValidationException $e) {
    echo "❌ Validation failed: " . $e->getMessage();
}
```