<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\Config\ConfigLoader;
use Dino\Core\Config\Parsers\JsonConfigParser;
use Dino\Core\Config\Parsers\YamlConfigParser;
use Dino\Core\Config\HierarchicalConfigMerger;

// Setup loader and parsers
$loader = new ConfigLoader();
$loader->addParser(new JsonConfigParser());
$loader->addParser(new YamlConfigParser());

// Create sample config files
$config1Path = __DIR__ . '/config1.json';
$config2Path = __DIR__ . '/config2.yaml';

file_put_contents($config1Path, json_encode([
    'app' => [
        'name' => 'Dino Library',
        'version' => '1.1.0',
        'debug' => true
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
], JSON_PRETTY_PRINT));

file_put_contents($config2Path, <<<YAML
app:
  debug: false
  environment: production
database:
  host: 127.0.0.1
  name: dino_db
YAML);

// Load and merge configs
$config1 = $loader->load($config1Path);
$config2 = $loader->load($config2Path);

$merger = new HierarchicalConfigMerger();
$merged = $merger->merge($config1, $config2);

// Output result
echo "Merged Configuration:\n";
print_r($merged);

// Cleanup
unlink($config1Path);
unlink($config2Path);
