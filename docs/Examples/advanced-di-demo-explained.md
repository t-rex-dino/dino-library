# 🔗 Advanced Dependency Injection Demo Explained

This demo showcases `DependencyResolver` and `ParameterResolver` for auto-wiring and advanced dependency resolution.

## 🔧 Code Walkthrough

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

$container = new ServiceContainer();
$resolver = new DependencyResolver($container);

$controller = $resolver->resolve(Controller::class);
echo $controller->handle();
```

## 📊 Expected Output

```

🚀 Dino Library - Advanced DI Demo
=========================================
Connected to database - Data fetched - Controller handled request
```

## ✅ Key Takeaways

*   Auto-wiring resolves dependencies based on constructor type hints.
*   Services are cached in the container for reuse.
*   Demonstrates how complex dependency chains can be resolved automatically.