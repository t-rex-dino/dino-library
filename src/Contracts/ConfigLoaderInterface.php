<?php
declare(strict_types=1);

namespace Dino\Contracts;

interface ConfigLoaderInterface
{
    public function load(string $filePath): array;
}
