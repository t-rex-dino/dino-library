# 🏷️ ServiceTagRegistry

The `ServiceTagRegistry` manages tags assigned to services for grouping and retrieval.

## 🔧 Usage

```

$registry->tag('utility', ['logger', 'cache']);
$services = $registry->getTaggedServices('utility');
```

## 📖 Methods

*   `tag(string $tag, array $serviceIds)` – assign services to a tag
*   `getTaggedServices(string $tag)` – retrieve services by tag

## ✅ Benefits

*   Organizes related services
*   Supports flexible grouping
*   Improves discoverability