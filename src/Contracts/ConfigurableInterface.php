<?php

namespace Dino\Contracts;

interface ConfigurableInterface
{
    /**
     * Set configuration values.
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config): void;

    /**
     * Retrieve current configuration.
     *
     * @return array
     */
    public function getConfig(): array;
}
