<?php
declare(strict_types=1);

namespace Dino\Core\Config\Parsers;

use Dino\Contracts\ConfigParserInterface;
use Dino\Exceptions\InvalidConfigFormatException;

class JsonConfigParser implements ConfigParserInterface
{
    public function parse(string $content): array
    {
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidConfigFormatException(
                'JSON parsing error: ' . json_last_error_msg()
            );
        }
        
        return $data ?? [];
    }
    
    public function supports(string $format): bool
    {
        return strtolower($format) === 'json';
    }
}