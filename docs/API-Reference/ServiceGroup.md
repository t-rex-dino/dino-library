# 📦 ServiceGroup

The `ServiceGroup` represents a collection of services with shared metadata and options.

## 🔧 Usage

```

$group = new ServiceGroup('communication', [
    'mailer' => ['priority' => 'high'],
    'notifier' => ['priority' => 'low']
]);
```

## 📖 Methods

*   `__construct(string $name, array $services)` – create a new group
*   `getServices()` – return all services in the group
*   `getMetadata(string $serviceId)` – return metadata for a specific service

## ✅ Benefits

*   Centralized metadata management
*   Flexible extension for future options
*   Improves service organization