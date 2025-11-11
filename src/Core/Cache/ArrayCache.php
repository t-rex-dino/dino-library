<?php
declare(strict_types=1);

namespace Dino\Core\Cache;

use Dino\Contracts\CacheInterface;

class ArrayCache implements CacheInterface
{
    private array $storage = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->storage[$key] ?? $default;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->storage[$key] = $value;
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->storage[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->storage = [];
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->storage);
    }
}
