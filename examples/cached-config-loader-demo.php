<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\Config\ConfigLoader;
use Dino\Core\Config\CachedConfigLoader;
use Dino\Core\Config\Parsers\JsonConfigParser;
use Dino\Core\Cache\ArrayCache;

// Create a temporary config file
$filePath = __DIR__ . '/temp-config.json';
file_put_contents($filePath, json_encode([
    'app' => ['name' => 'Dino', 'debug' => true]
], JSON_PRETTY_PRINT));

// Setup loader and cache
$loader = new ConfigLoader();
$loader->addParser(new JsonConfigParser());

$cache = new ArrayCache();
$cachedLoader = new CachedConfigLoader($loader, $cache);

// First load (from file)
$config = $cachedLoader->load($filePath);
echo "First load:\n";
print_r($config);

// Second load (from cache)
$configCached = $cachedLoader->load($filePath);
echo "Second load (from cache):\n";
print_r($configCached);

// Cleanup
unlink($filePath);
