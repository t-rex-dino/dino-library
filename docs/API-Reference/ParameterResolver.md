# 🧩 ParameterResolver

The `ParameterResolver` resolves constructor parameters using type hints, defaults, and container entries.

## 🔧 Usage

```

$parameterResolver = new ParameterResolver();
$value = $parameterResolver->resolve($reflectionParameter, $container);
```

## 📖 Methods

*   `resolve(ReflectionParameter $parameter, ServiceContainer $container)` – resolve a parameter value

## ✅ Benefits

*   Supports auto-wiring via type hints
*   Detects circular dependencies
*   Handles default values and nullability