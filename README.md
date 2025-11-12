![Dino Library Banner](docs/assets/dino-banner.jpeg)

# 🦕 Dino Library

Dino Library is a lightweight, extensible PHP library designed to manage services, configurations, and dependency injection in a clean and modular way.

- - -

## 🚀 Installation

```

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

docs/
├── Guides/              // Conceptual guides
├── API-Reference/       // API documentation
├── Examples/            // Example explanations
├── Tutorials/           // Step-by-step tutorials
```

- - -

## 🧩 Quick Usage

### LibraryManager

```

use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('logger', new Logger());
$logger = $manager->get('logger');
```

### ConfigHandler with Validation

```

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;

$config = new ConfigHandler();
$config->registerValidator(new RequiredValidator());
$config->set('app.name', 'Dino Library');
```

### ServiceContainer with Factory

```

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

```

$container->singleton('heavyService', fn() => new HeavyService(), true);
$service = $container->get('heavyService'); // created only when accessed
```

### 🔗 Dependency Injection

```

$resolver = new DependencyResolver($container);
$controller = $resolver->resolve(Controller::class);
```

- - -

## 🧪 Running Tests

```

vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/Unit
```

- - -

## 📚 Examples

Example files are located in the `examples/` directory:

*   basic-usage.php
*   config-handler-demo.php
*   service-container-demo.php
*   config-validation-demo.php
*   service-provider-demo.php (New in 1.2.0)
*   lazy-loading-demo.php (New in 1.2.0)
*   service-tagging-demo.php (New in 1.2.0)
*   advanced-di-demo.php (New in 1.2.0)

```

php examples/advanced-di-demo.php
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