<?php

namespace Dino\Contracts;

interface ServiceInterface
{
    /**
     * Boot the service.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Check if the service has been booted.
     *
     * @return bool
     */
    public function isBooted(): bool;
}
