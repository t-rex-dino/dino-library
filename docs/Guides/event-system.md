## 🧩 Event System

The Dino Library includes a lightweight event system to handle service lifecycle hooks and cross-component communication.

### EventDispatcherInterface

This interface defines the contract for dispatching and subscribing to events:

```php

interface EventDispatcherInterface {
    public function dispatch(string $eventName, array $payload = []): void;
    public function subscribe(string $eventName, callable $listener): void;
    public function unsubscribe(string $eventName, callable $listener): void;
    public function getListeners(string $eventName): array;
    public function hasListeners(string $eventName): bool;
}
```

### ServiceLifecycleEvents

Predefined constants for service lifecycle events:

*   `service.before_create`
*   `service.after_create`
*   `service.before_init`
*   `service.after_init`
*   `service.before_destroy`
*   `service.after_destroy`

### Usage Example

```php

$dispatcher = new EventDispatcher();

$dispatcher->subscribe('service.after_create', function(array $payload) {
    echo "Service created: " . $payload['serviceName'];
});

$dispatcher->dispatch('service.after_create', ['serviceName' => 'Logger']);
```

### Extensibility

*   Multiple listeners can be attached to the same event
*   Listeners receive the event payload as an associative array
*   Useful for logging, profiling, or dynamic service decoration