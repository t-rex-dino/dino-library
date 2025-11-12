<?php
declare(strict_types=1);

namespace Dino\Core;

use Dino\Contracts\FactoryInterface;

class LazyServiceWrapper implements FactoryInterface
{
    private $factory;
    private ?object $instance = null;
    
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }
    
    public function create(...$params): object
    {
        if ($this->instance === null) {
            $this->instance = ($this->factory)(...$params);
        }
        return $this->instance;
    }
    
    public function isInitialized(): bool
    {
        return $this->instance !== null;
    }
}