<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dino\Core\ServiceContainer;
use Dino\Core\DependencyResolver;

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

echo "🚀 Dino Library - Advanced DI Demo" . PHP_EOL;
echo "=========================================" . PHP_EOL;

$container = new ServiceContainer();
$resolver = new DependencyResolver($container);

$controller = $resolver->resolve(Controller::class);
echo $controller->handle() . PHP_EOL;
