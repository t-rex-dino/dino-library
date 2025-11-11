<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\Config\HierarchicalConfigMerger;

class HierarchicalConfigMergerTest extends TestCase
{
    public function testMergeConfigs(): void
    {
        $merger = new HierarchicalConfigMerger();
        $config1 = ['app' => ['name' => 'Dino', 'debug' => false]];
        $config2 = ['app' => ['debug' => true]];

        $result = $merger->merge($config1, $config2);
        $this->assertEquals(['app' => ['name' => 'Dino', 'debug' => true]], $result);
    }
}
