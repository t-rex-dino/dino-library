# Advanced Usage of Dino Library

This tutorial explores advanced features and patterns in Dino Library, including dynamic service creation, configuration-driven factories, and integration strategies.

## Dynamic Service Creation

You can create services dynamically using parameters passed to factories:

```php
class DatabaseFactory implements FactoryInterface {
    public function create(...$params): object {
        [$host, $user, $pass] = $params;
        return new DatabaseConnection($host, $user, $pass);
    }
}

$container->addFactory('db', new DatabaseFactory());
$db = $container->get('db', 'localhost', 'root', 'secret');
```

## Configuration-Driven Factories

Use `ConfigHandler` to inject configuration into factories:

```php
class MailerFactory implements FactoryInterface {
    private ConfigHandler $config;

    public function __construct(ConfigHandler $config) {
        $this->config = $config;
    }

    public function create(...$params): object {
        $settings = $this->config->get('mail');
        return new Mailer($settings['host'], $settings['port']);
    }
}

$config->loadFromFile('config/mail.php');
$container->addFactory('mailer', new MailerFactory($config));
$mailer = $container->get('mailer');
```

## Service Composition

Compose services by injecting dependencies:

```php
class AppServiceFactory implements FactoryInterface {
    public function create(...$params): object {
        $logger = $params[0];
        $mailer = $params[1];
        return new AppService($logger, $mailer);
    }
}

$container->addFactory('app', new AppServiceFactory());
$appService = $container->get('app', $container->get('logger'), $container->get('mailer'));
```

## Environment-Specific Configuration

Load different config files based on environment:

```php
$env = getenv('APP_ENV') ?: 'production';
$config->loadFromFile("config/{$env}.php");
```

## Testing with Isolated Services

Clear and re-register services for testing:

```php
$container->clear();
$container->addFactory('mockLogger', new MockLoggerFactory());
```

## Best Practices

*   Use factories to encapsulate service creation logic
*   Inject configuration and dependencies explicitly
*   Isolate services during testing
*   Use environment variables to switch configurations
*   Avoid hardcoding values inside factories

## Related Tutorials

*   [Getting Started](getting-started.md)
*   [Troubleshooting](troubleshooting.md)
