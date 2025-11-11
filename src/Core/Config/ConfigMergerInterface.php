<?php
declare(strict_types=1);

namespace Dino\Core\Config;

interface ConfigMergerInterface
{
    /**
     * Merges configuration arrays hierarchically
     */
    public function merge(array ...$configs): array;
}