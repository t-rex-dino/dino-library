![Dino Library Banner](docs/assets/dino-banner.jpeg)

 Dino Library README - v1.2.1

# 🦕 Dino Library

Dino Library is a lightweight, extensible PHP library designed to manage services, configurations, and dependency injection in a clean and modular way.

## 🚀 Installation

```
composer require t-rex-dino/dino-library
```

## 📁 Project Structure

```

src/
├── Contracts/           // Core interfaces
├── Exceptions/          // Context-aware exception classes
├── Core/                // Main library classes

tests/
├── Unit/                // PHPUnit unit tests

examples/
├── *.php                // Practical usage examples

docs/
├── Guides/              // Conceptual guides
├── API-Reference/       // API documentation
├── Examples/            // Example explanations
├── Tutorials/           // Step-by-step tutorials
    
```

## 🧩 Quick Usage

### LibraryManager

```php

use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('logger', new Logger());
$logger = $manager->get('logger');
    
```

### ConfigHandler with Validation

```php

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;

$config = new ConfigHandler();
$config->registerValidator(new RequiredValidator());
$config->set('app.name', 'Dino Library');
    
```

### ServiceContainer with Factory

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

### ⚡ Lazy Loading

```php

$container->singleton('heavyService', fn() => new HeavyService(), true);
$service = $container->get('heavyService'); // created only when accessed
    
```

### 🔗 Dependency Injection

```php

$resolver = new DependencyResolver($container);
$controller = $resolver->resolve(Controller::class);
    
```

## 🛡️ Error Handling System (New in v1.2.1)

Dino Library v1.2.1 introduces a context-aware exception system with standardized error codes and structured context data.

*   `ConfigValidationException` – thrown when validation fails
*   `ConfigNotFoundException` – thrown when a configuration key is missing
*   `ServiceNotFoundException` – thrown when a service cannot be found
*   `ServiceResolutionException` – thrown when a service cannot be resolved

```php

use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

try {
    $validator->validate("abc", ["rule" => "type:int", "configKey" => "db.port"]);
} catch (ConfigValidationException $e) {
    echo ErrorMessageFormatter::format($e);
}
    
```

## 🧪 Running Tests

```
vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/Unit
```

## 📚 Examples

Example files are located in the `examples/` directory:

*   basic-usage.php
*   config-handler-demo.php
*   service-container-demo.php
*   config-validation-demo.php
*   error-handling-demo.php (New in 1.2.1)
*   advanced-validation-demo.php (New in 1.2.1)
*   service-provider-demo.php (New in 1.2.0)
*   lazy-loading-demo.php (New in 1.2.0)
*   service-tagging-demo.php (New in 1.2.0)
*   advanced-di-demo.php (New in 1.2.0)

## 🤝 Contributing

Contributions are welcome! Please submit issues, pull requests, or suggestions. For guidelines, refer to `CONTRIBUTING.md`.

## 🧙‍♂️ Development Team

*   **t-rex-dino** – Project Manager & Repository Maintainer
*   **DeepSeek AI** – Architect & Optimization Specialist
*   **Copilot** – Lead Developer & Documentation Specialist

## 📄 License

This project is licensed under the MIT License.