<?php
declare(strict_types=1);

namespace Dino\Contracts;

interface ConfigParserInterface
{
    /**
     * Parses raw config content into array
     */
    public function parse(string $content): array;
    
    /**
     * Checks if parser supports the given format
     */
    public function supports(string $format): bool;
}