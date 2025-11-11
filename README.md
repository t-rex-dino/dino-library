![Dino Library Banner](docs/assets/dino-banner.jpeg)

# 🦕 Dino Library

Dino Library is a lightweight, extensible PHP library designed to manage services, configurations, and dependency injection in a clean and modular way.

- - -

## 🚀 Installation

```bash
composer require t-rex-dino/dino-library
```

- - -

## 📁 Project Structure

```

src/
├── Contracts/           // Core interfaces
├── Exceptions/          // Custom exception classes
├── Core/                // Main library classes

tests/
├── Unit/                // PHPUnit unit tests

examples/
├── *.php                // Practical usage examples
```

- - -

## 🧩 Quick Usage

### LibraryManager

```php

use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('logger', new Logger());
$logger = $manager->get('logger');
```

### ConfigHandler

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;

$config = new ConfigHandler();

// Register validators
$config->registerValidator(new RequiredValidator());
$config->registerValidator(new TypeValidator());
$config->registerValidator(new RangeValidator());
$config->registerValidator(new RegexValidator());

// Define rules
$config->setValidationRules([
    'app.name'  => ['required', 'type:string'],
    'app.port'  => ['required', 'type:int', 'range'],
    'app.email' => ['required', 'regex']
]);

// Valid configuration
$config->set('app.name', 'Dino Library');
$config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
$config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);

// Invalid configuration
try {
    $config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
} catch (ValidationException $e) {
    echo "❌ Validation failed: " . $e->getMessage();
}
```

### ServiceContainer

```php

use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface {
    public function create(...$params): object {
        return new Logger();
    }
}

$container = new ServiceContainer();
$container->addFactory('logger', new LoggerFactory());
$logger = $container->get('logger');
```

- - -

## 🧪 Running Tests

```bash
vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/Unit
```

- - -

## 📚 Examples

Example files are located in the `examples/` directory:

*   basic-usage.php
*   config-handler-demo.php
*   service-container-demo.php
*   config-validation-demo.php (New)

Run them using:

```bash
php examples/config-validation-demo.php
```

- - -

## 🤝 Contributing

Contributions are welcome! Please submit issues, pull requests, or suggestions. For guidelines, refer to `CONTRIBUTING.md`.

- - -

## 🧙‍♂️ Development Team

*   **t-rex-dino** – Project Manager & Repository Maintainer
*   **DeepSeek AI** – Architect & Optimization Specialist
*   **Copilot** – Lead Developer & Documentation Specialist

- - -

## 📄 License

This project is licensed under the MIT License.