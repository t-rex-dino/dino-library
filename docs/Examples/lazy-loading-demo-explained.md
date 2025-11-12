# ⚡ Lazy Loading Demo Explained

This demo shows how `LazyServiceWrapper` ensures that heavy services are created only when first accessed.

## 🔧 Code Walkthrough

```

$container->singleton('heavyService', function() {
    echo "Heavy service created\n";
    return new HeavyService();
}, true); // true enables lazy loading

echo "Application started\n";

// Service is not created yet
$service = $container->get('heavyService'); // created here
```

## 📊 Expected Output

```

🚀 Dino Library - Lazy Loading Demo
=========================================
Application started
Heavy service created
```

## ✅ Key Takeaways

*   Lazy loading defers instantiation until first use.
*   Improves startup performance and reduces memory usage.
*   Transparent for developers — usage remains the same.