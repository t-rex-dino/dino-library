<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\ServiceContainer;
use Dino\Core\AbstractServiceProvider;

// Define a simple service
class LoggerService {
    public function log(string $message): void {
        echo "📝 [LOG]: " . $message . PHP_EOL;
    }
}

// Define a service provider
class LoggerServiceProvider extends AbstractServiceProvider {
    protected array $provides = ['logger'];
    
    public function register(ServiceContainer $container): void {
        $this->bind($container, 'logger', new LoggerService());
        echo "✅ Logger service registered!" . PHP_EOL;
    }
}

// Define a deferred service provider
class HeavyService {
    public function process(): string {
        return "Heavy processing completed!" . PHP_EOL;
    }
}

class HeavyServiceProvider extends AbstractServiceProvider {
    protected array $provides = ['heavy.service'];
    protected bool $deferred = true;
    
    public function register(ServiceContainer $container): void {
        $this->factory($container, 'heavy.service', function() {
            echo "🏗️  Creating heavy service..." . PHP_EOL;
            return new HeavyService();
        });
        echo "✅ Heavy service factory registered!" . PHP_EOL;
    }
}

// Demo execution
echo "🚀 Dino Library - Service Provider Demo" . PHP_EOL;
echo "=========================================" . PHP_EOL;

// Create container
$container = new ServiceContainer();

// Register providers
$loggerProvider = new LoggerServiceProvider();
$loggerProvider->register($container);

$heavyProvider = new HeavyServiceProvider();
$heavyProvider->register($container);

echo PHP_EOL . "📦 Using services:" . PHP_EOL;
echo "-----------------------------------------" . PHP_EOL;

// Use the logger service
$logger = $container->get('logger');
$logger->log("Hello from Dino Library!");

// Use the heavy service (will be created only when requested)
echo "➡️  Requesting heavy service..." . PHP_EOL;
$heavyService = $container->get('heavy.service');
echo $heavyService->process();

// Request heavy service again (should reuse existing instance)
echo "➡️  Requesting heavy service again..." . PHP_EOL;
$heavyService2 = $container->get('heavy.service');
echo $heavyService2->process();

echo PHP_EOL . "✅ Demo completed successfully!" . PHP_EOL;