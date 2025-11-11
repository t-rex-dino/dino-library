<?php
declare(strict_types=1);

namespace Dino\Core\Events;

use Dino\Contracts\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function dispatch(string $eventName, array $payload = []): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            $listener($payload);
        }
    }

    public function subscribe(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $this->listeners[$eventName][] = $listener;
    }

    public function unsubscribe(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        $this->listeners[$eventName] = array_filter(
            $this->listeners[$eventName],
            fn($registeredListener) => $registeredListener !== $listener
        );
    }

    public function getListeners(string $eventName): array
    {
        return $this->listeners[$eventName] ?? [];
    }

    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }
}