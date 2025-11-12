<?php
declare(strict_types=1);

namespace Dino\Core;

class ServiceTagRegistry
{
    private array $tags = [];

    /**
     * Add a tag to a service.
     */
    public function addTag(string $serviceId, string $tag): void
    {
        $this->tags[$serviceId][] = $tag;
    }

    /**
     * Get all tags for a given service.
     */
    public function getTags(string $serviceId): array
    {
        return $this->tags[$serviceId] ?? [];
    }

    /**
     * Get all services associated with a given tag.
     */
    public function getServicesByTag(string $tag): array
    {
        $services = [];
        foreach ($this->tags as $serviceId => $tags) {
            if (in_array($tag, $tags, true)) {
                $services[] = $serviceId;
            }
        }
        return $services;
    }

    /**
     * Tag a service that exists in the container.
     */
    public function tagService(ServiceContainer $container, string $serviceId, array $tags): void
    {
        foreach ($tags as $tag) {
            $this->addTag($serviceId, $tag);
        }
    }

    /**
     * Retrieve actual service instances from the container by tag.
     */
    public function getTaggedServices(ServiceContainer $container, string $tag): array
    {
        $services = [];
        foreach ($this->getServicesByTag($tag) as $serviceId) {
            if ($container->has($serviceId)) {
                $services[$serviceId] = $container->get($serviceId);
            }
        }
        return $services;
    }
}
