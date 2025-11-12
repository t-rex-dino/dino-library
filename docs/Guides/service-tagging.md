# 🏷️ Service Tagging & Grouping Guide

Dino Library provides `ServiceTagRegistry` and `ServiceGroup` to organize services with tags and metadata.

## 🔧 Concept

*   Tags allow grouping related services under a common label.
*   Service groups provide metadata and options for collections of services.

## 📐 Usage

```

// Tagging services
$registry->tag('utility', ['logger', 'cache']);

// Creating a service group
$group = new ServiceGroup('communication', [
    'mailer' => ['priority' => 'high'],
    'notifier' => ['priority' => 'low']
]);
```

## 🧪 Example

See [Service Tagging Demo Explained](../Examples/service-tagging-demo-explained.md) for a practical walkthrough.

## ✅ Benefits

*   Improved organization of services
*   Flexible metadata management
*   Supports advanced extensions