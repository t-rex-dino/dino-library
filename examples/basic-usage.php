<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\LibraryManager;

$manager = new LibraryManager();
$manager->register('greeting', new class {
    public function sayHello(): void
    {
        echo "Hello from Dino Library!" . PHP_EOL;
    }
});

$service = $manager->get('greeting');
$service->sayHello();
