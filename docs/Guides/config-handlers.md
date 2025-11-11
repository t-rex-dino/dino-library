## 🧠 CachedConfigLoader

The `CachedConfigLoader` wraps any `ConfigLoaderInterface` and adds caching support using a PSR-16-like `CacheInterface`.

### Usage

```php

$loader = new ConfigLoader();
$loader->addParser(new JsonConfigParser());

$cache = new ArrayCache();
$cachedLoader = new CachedConfigLoader($loader, $cache);

$config = $cachedLoader->load('config.json');
```

### Cache Invalidation

```php

$cachedLoader->invalidate('config.json');
```

### Cache Key Strategy

*   Key is based on file path + `filemtime()`
*   Ensures cache is invalidated when file changes

### Default TTL

*   Default TTL is 3600 seconds (1 hour)
*   Can be customized via constructor

### ArrayCache Example

```php

$cache = new ArrayCache();
$cache->set('key', ['value' => 123]);
$data = $cache->get('key');
```