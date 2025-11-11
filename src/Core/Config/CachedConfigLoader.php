<?php
declare(strict_types=1);

namespace Dino\Core\Config;

use Dino\Contracts\CacheInterface;
use Dino\Contracts\ConfigLoaderInterface;

class CachedConfigLoader implements ConfigLoaderInterface
{
    public function __construct(
        private ConfigLoaderInterface $loader,
        private CacheInterface $cache,
        private int $defaultTtl = 3600
    ) {}

    public function load(string $filePath): array
    {
        $cacheKey = $this->generateCacheKey($filePath);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $config = $this->loader->load($filePath);
        $this->cache->set($cacheKey, $config, $this->defaultTtl);

        return $config;
    }

    public function invalidate(string $filePath): void
    {
        $cacheKey = $this->generateCacheKey($filePath);
        $this->cache->delete($cacheKey);
    }

    private function generateCacheKey(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Config file not found: $filePath");
        }

        $fileHash = md5($filePath . filemtime($filePath));
        return 'config_' . $fileHash;
    }
}
