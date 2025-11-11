<?php
declare(strict_types=1);

namespace Dino\Core\Config;

class HierarchicalConfigMerger implements ConfigMergerInterface
{
    /**
     * Merges configuration arrays while preserving hierarchy
     */
    public function merge(array ...$configs): array
    {
        if (empty($configs)) {
            return [];
        }

        return array_replace_recursive(...$configs);
    }
}