# 🛠️ Service Provider Demo Explained

This demo illustrates how to use `AbstractServiceProvider` for modular and deferred service registration.

## 🔧 Code Walkthrough

```

class LoggerServiceProvider extends AbstractServiceProvider {
    protected array $provides = ['logger'];

    public function register(ServiceContainer $container): void {
        $container->singleton('logger', function() {
            return new Logger();
        });
    }
}

$container = new ServiceContainer();
$container->register(new LoggerServiceProvider());

$logger = $container->get('logger');
$logger->info("Service provider demo executed");
```

## 📊 Expected Output

```

🚀 Dino Library - Service Provider Demo
=========================================
[INFO] Service provider demo executed
```

## ✅ Key Takeaways

*   Service providers encapsulate registration logic.
*   Deferred providers load services only when needed.
*   Improves modularity and maintainability of the codebase.