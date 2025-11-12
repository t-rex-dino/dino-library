<?php
declare(strict_types=1);

namespace Dino\Core\Utils;

use Dino\Contracts\FactoryInterface;

class ClosureFactory implements FactoryInterface
{
    private $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function create(...$params): object
    {
        return ($this->closure)(...$params);
    }
}
