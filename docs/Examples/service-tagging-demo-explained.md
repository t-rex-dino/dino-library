# 🏷️ Service Tagging Demo Explained

This demo illustrates how to use `ServiceTagRegistry` and `ServiceGroup` to organize services with tags and metadata.

## 🔧 Code Walkthrough

```

$registry->tag('utility', ['logger', 'cache']);

$group = new ServiceGroup('communication', [
    'mailer' => ['priority' => 'high'],
    'notifier' => ['priority' => 'low']
]);

echo "Services tagged with 'utility': logger, cache";
echo "Services in group 'communication': mailer, notifier";
```

## 📊 Expected Output

```

🚀 Dino Library - Service Tagging Demo
=========================================
📌 Services tagged with 'utility': logger, cache
📦 Services in group 'communication': mailer, notifier
🔧 Core services:
 - logger
 - mailer
```

## ✅ Key Takeaways

*   Tags allow grouping related services under a common label.
*   Service groups provide metadata and options for collections of services.
*   Improves organization and flexibility in service management.