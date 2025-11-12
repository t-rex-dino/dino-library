# 🔗 Advanced Dependency Injection Tutorial

This tutorial demonstrates how to use `DependencyResolver` and `ParameterResolver` for advanced dependency injection in Dino Library.

## 📚 Step 1: Define Classes

```

class Database {
    public function connect(): string { return "Connected to database"; }
}

class Repository {
    private Database $db;
    public function __construct(Database $db) { $this->db = $db; }
    public function fetch(): string { return $this->db->connect() . " - Data fetched"; }
}

class Controller {
    private Repository $repo;
    public function __construct(Repository $repo) { $this->repo = $repo; }
    public function handle(): string { return $this->repo->fetch() . " - Controller handled request"; }
}
```

## 📚 Step 2: Setup Container and Resolver

```

$container = new ServiceContainer();
$resolver = new DependencyResolver($container);
```

## 📚 Step 3: Resolve Controller

```

$controller = $resolver->resolve(Controller::class);
echo $controller->handle();
```

## 📊 Expected Output

```

🚀 Dino Library - Advanced Dependency Injection
=========================================
Connected to database - Data fetched - Controller handled request
```

## ✅ Summary

*   Auto-wiring resolves dependencies based on constructor type hints.
*   Interface binding and contextual overrides are supported.
*   Circular dependency detection ensures safe resolution.