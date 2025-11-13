<?php
declare(strict_types=1);

namespace Dino\Core\Config\Parsers;

use Dino\Contracts\ConfigParserInterface;
use Dino\Exceptions\ConfigParserException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlConfigParser implements ConfigParserInterface
{
    public function parse(string $content): array
    {
        try {
            $data = Yaml::parse($content);
            return is_array($data) ? $data : [];
        } catch (ParseException $e) {
            throw new ConfigParserException(
                'config.yaml',
                ['error' => $e->getMessage()]
            );
        }
    }
    
    public function supports(string $format): bool
    {
        return in_array(strtolower($format), ['yaml', 'yml']);
    }
}
