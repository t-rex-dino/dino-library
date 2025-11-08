<?php

declare(strict_types=1);

namespace Dino\Contracts;

interface FactoryInterface
{
    /**
     * Create and return an instance of a service or object.
     *
     * @param mixed ...$parameters Optional parameters for instantiation
     * @return object
     */
    public function create(mixed ...$parameters): object;
}
