<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\ServiceContainer;
use Dino\Contracts\FactoryInterface;

class LoggerFactory implements FactoryInterface
{
    public function create(mixed ...$parameters): object
    {
        return new class {
            public function log(string $message): void
            {
                echo "[LOG] " . $message . PHP_EOL;
            }
        };
    }
}

$container = new ServiceContainer();
$container->addFactory('logger', new LoggerFactory());

$logger = $container->get('logger');
$logger->log("ServiceContainer is working!");
