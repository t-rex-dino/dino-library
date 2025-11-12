# 🔗 DependencyResolver

The `DependencyResolver` automatically resolves and instantiates classes using constructor injection.

## 🔧 Usage

```

$resolver = new DependencyResolver($container);
$controller = $resolver->resolve(Controller::class);
```

## 📖 Methods

*   `resolve(string $className)` – auto-wire and instantiate a class
*   `bindInterface(string $interface, string $implementation)` – bind an interface to an implementation
*   `resolveWith(string $className, array $customParameters)` – resolve with contextual parameters

## ✅ Benefits

*   Automatic dependency injection
*   Supports interface binding
*   Contextual overrides for flexibility