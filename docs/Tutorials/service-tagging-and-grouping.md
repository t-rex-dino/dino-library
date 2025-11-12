# 🏷️ Service Tagging & Grouping Tutorial

This tutorial explains how to use `ServiceTagRegistry` and `ServiceGroup` to organize services in Dino Library.

## 📚 Step 1: Tag Services

```

$registry->tag('utility', ['logger', 'cache']);
```

## 📚 Step 2: Create a Service Group

```

$group = new ServiceGroup('communication', [
    'mailer' => ['priority' => 'high'],
    'notifier' => ['priority' => 'low']
]);
```

## 📚 Step 3: Retrieve Tagged Services

```

$services = $registry->getTaggedServices('utility');
print_r($services);
```

## 📊 Expected Output

```

🚀 Dino Library - Service Tagging & Grouping
=========================================
📌 Services tagged with 'utility': logger, cache
📦 Services in group 'communication': mailer, notifier
```

## ✅ Summary

*   Tags allow grouping related services under a common label.
*   Service groups provide metadata and options for collections of services.
*   Improves organization and flexibility in service management.