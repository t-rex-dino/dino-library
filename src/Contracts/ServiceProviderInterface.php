<?php

namespace Dino\Contracts;

use Dino\Core\ServiceContainer;

interface ServiceProviderInterface
{
    /**
     * Register services into the container.
     *
     * @param ServiceContainer $container
     * @return void
     */
    public function register(ServiceContainer $container): void;

    /**
     * Boot services after registration.
     *
     * @param ServiceContainer $container
     * @return void
     */
    public function boot(ServiceContainer $container): void;
}
