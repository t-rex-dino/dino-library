# ⚡ LazyServiceWrapper

The `LazyServiceWrapper` defers the instantiation of a service until it is first accessed.

## 🔧 Usage

```

$container->singleton('heavyService', function() {
    return new HeavyService();
}, true); // true enables lazy loading
```

## 📖 Methods

*   `__construct(callable $factory)` – wraps a factory for deferred execution
*   `get()` – returns the service instance, creating it if not already created

## ✅ Benefits

*   Improves startup performance
*   Reduces memory usage
*   Transparent for developers