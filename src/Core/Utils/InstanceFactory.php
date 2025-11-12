<?php
declare(strict_types=1);

namespace Dino\Core\Utils;

use Dino\Contracts\FactoryInterface;

class InstanceFactory implements FactoryInterface
{
    private object $instance;
    
    public function __construct(object $instance)
    {
        $this->instance = $instance;
    }
    
    public function create(...$params): object
    {
        return $this->instance;
    }
}