# ⚡ Lazy Loading Guide

The Dino Library introduces `LazyServiceWrapper` to optimize resource usage by creating services only when they are actually needed.

## 🔧 Concept

*   Lazy loading defers the creation of heavy or rarely used services until the first time they are accessed.
*   Improves performance and reduces memory footprint.

## 📐 Usage

```

$container->singleton('heavyService', function() {
    return new HeavyService();
}, true); // true enables lazy loading
```

## 🧪 Example

See [Lazy Loading Demo Explained](../Examples/lazy-loading-demo-explained.md) for a practical walkthrough.

## ✅ Benefits

*   Efficient resource management
*   Improved startup time
*   Transparent usage for developers