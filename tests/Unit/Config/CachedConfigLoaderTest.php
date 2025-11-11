<?php
declare(strict_types=1);

namespace Dino\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Dino\Core\Config\CachedConfigLoader;
use Dino\Core\Cache\ArrayCache;
use Dino\Core\Config\ConfigLoader;
use Dino\Core\Config\Parsers\JsonConfigParser;

class CachedConfigLoaderTest extends TestCase
{
    public function testLoadFromCache(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'dino_') . '.json';
        file_put_contents($filePath, '{"app":{"name":"Dino"}}');

        $baseLoader = new ConfigLoader();
        $baseLoader->addParser(new JsonConfigParser());

        $cache = new ArrayCache();
        $cachedLoader = new CachedConfigLoader($baseLoader, $cache, 60);

        // First load: should parse and cache
        $first = $cachedLoader->load($filePath);
        $this->assertEquals(['app' => ['name' => 'Dino']], $first);

        // Second load: should come from cache
        $second = $cachedLoader->load($filePath);
        $this->assertEquals($first, $second);

        // Invalidate and reload
        $cachedLoader->invalidate($filePath);
        $third = $cachedLoader->load($filePath);
        $this->assertEquals(['app' => ['name' => 'Dino']], $third);

        unlink($filePath);
    }
}