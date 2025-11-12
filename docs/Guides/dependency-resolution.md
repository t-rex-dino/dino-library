# 🔗 Dependency Resolution Guide

The Dino Library introduces `DependencyResolver` and `ParameterResolver` for advanced dependency injection.

## 🔧 Concept

*   Auto-wiring based on constructor type hints
*   Interface binding to connect contracts with implementations
*   Contextual binding with custom parameters
*   Circular dependency detection

## 📐 Usage

```

// Auto-wiring
$controller = $resolver->resolve(Controller::class);

// Interface binding
$resolver->bindInterface(EngineInterface::class, Engine::class);

// Contextual binding
$service = $resolver->resolveWith(Service::class, ['config' => $customConfig]);
```

## 🧪 Example

See [Advanced DI Demo Explained](../Examples/advanced-di-demo-explained.md) for a practical walkthrough.

## ✅ Benefits

*   Automatic resolution of complex dependencies
*   Flexible binding strategies
*   Safe detection of circular dependencies