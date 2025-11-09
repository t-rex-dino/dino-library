<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\ConfigHandler;

$config = new ConfigHandler();
$config->set('database.host', 'localhost');
$config->set('database.port', 3306);

echo "Host: " . $config->get('database.host') . PHP_EOL;
echo "Port: " . $config->get('database.port') . PHP_EOL;
