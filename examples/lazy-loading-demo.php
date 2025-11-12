<?php
declare(strict_types=1);

use Dino\Core\LazyServiceWrapper;

class Logger {
    public function log(string $message): void {
        echo "[LOG] " . $message . PHP_EOL;
    }
}

$wrapper = new LazyServiceWrapper(fn() => new Logger());

echo "Before initialization..." . PHP_EOL;
echo "Is initialized? " . ($wrapper->isInitialized() ? "Yes" : "No") . PHP_EOL;

$logger = $wrapper->create();
$logger->log("Lazy loading demo running successfully!");

echo "After initialization..." . PHP_EOL;
echo "Is initialized? " . ($wrapper->isInitialized() ? "Yes" : "No") . PHP_EOL;
