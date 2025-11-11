<?php
declare(strict_types=1);

namespace Dino\Contracts;

interface EventDispatcherInterface
{
    /**
     * Dispatch an event with optional payload
     */
    public function dispatch(string $eventName, array $payload = []): void;

    /**
     * Subscribe a listener to an event
     */
    public function subscribe(string $eventName, callable $listener): void;

    /**
     * Unsubscribe a listener from an event
     */
    public function unsubscribe(string $eventName, callable $listener): void;

    /**
     * Get all listeners for an event
     */
    public function getListeners(string $eventName): array;

    /**
     * Check if an event has listeners
     */
    public function hasListeners(string $eventName): bool;
}