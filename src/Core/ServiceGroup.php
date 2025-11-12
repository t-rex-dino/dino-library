<?php
declare(strict_types=1);

namespace Dino\Core;

class ServiceGroup
{
    private string $name;
    private array $services = [];
    private array $metadata = [];

    public function __construct(string $name, array $metadata = [])
    {
        $this->name = $name;
        $this->metadata = $metadata;
    }

    /**
     * Add a service to the group with optional configuration.
     */
    public function addService(string $serviceId, array $options = []): void
    {
        $this->services[$serviceId] = $options;
    }

    /**
     * Get all services in the group.
     */
    public function getServices(): array
    {
        return array_keys($this->services);
    }

    /**
     * Get services with their options.
     */
    public function getServicesWithOptions(): array
    {
        return $this->services;
    }

    /**
     * Get group name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get metadata associated with the group.
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Update metadata for the group.
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
