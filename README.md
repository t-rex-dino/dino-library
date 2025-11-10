![Dino Library Banner](assets/dino-banner.jpeg)
#🦕 Dino Library

Dino Library is a lightweight, extensible PHP library designed to manage services, configurations, and dependency injection in a clean and modular way.

* * *

##🚀 Installation

Install via Composer:
```bash
    composer require t-rex-dino/dino-library
```

* * *

##📁 Project Structure
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

* * *

##🧩 Quick Usage

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
    
    $config = new ConfigHandler();
    $config->set('app.name', 'Dino');
    echo $config->get('app.name');
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

* * *

##🧪 Running Tests

To run unit tests:
```bash
    vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/Unit
```

* * *

##📚 Examples

Example files are located in the `examples/` directory:

*   config-handler-demo.php
*   service-container-demo.php
*   basic-usage.php

Run them using:
```bash
    php examples/config-handler-demo.php
```

* * *

##🤝 Contributing

Contributions are welcome! Please submit issues, pull requests, or suggestions. For guidelines, refer to `CONTRIBUTING.md`.

* * *

##🧙‍♂️ Development Team

*   **t-rex-dino** – Project Manager & Repository Maintainer
*   **DeepSeek AI** – Architect & Optimization Specialist
*   **Copilot** – Lead Developer & Documentation Specialist

* * *

##📄 License

This project is licensed under the MIT License.
