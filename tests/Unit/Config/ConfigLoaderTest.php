<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\Config\ConfigLoader;
use Dino\Core\Config\Parsers\JsonConfigParser;

class ConfigLoaderTest extends TestCase
{
    public function testLoadJsonFile(): void
    {
        $loader = new ConfigLoader();
        $loader->addParser(new JsonConfigParser());

        // create temp file
        $filePath = tempnam(sys_get_temp_dir(), 'dino_test_') . '.json';
        file_put_contents($filePath, '{"app":{"name":"Dino"}}');

        $result = $loader->load($filePath);
        $this->assertEquals(['app' => ['name' => 'Dino']], $result);

        // remove temp file
        unlink($filePath);
    }
}
