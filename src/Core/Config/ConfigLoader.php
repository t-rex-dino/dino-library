<?php
declare(strict_types=1);

namespace Dino\Core\Config;

use Dino\Contracts\ConfigLoaderInterface;
use Dino\Contracts\ConfigParserInterface;
use Dino\Exceptions\InvalidConfigFormatException;
use Dino\Core\Config\Parsers\JsonConfigParser;
use Dino\Core\Config\Parsers\YamlConfigParser;

class ConfigLoader implements ConfigLoaderInterface
{
    private array $parsers = [];

    public function __construct()
    {
        // Auto-register default parsers
        $this->addParser(new JsonConfigParser());
        $this->addParser(new YamlConfigParser());
    }

    public function addParser(ConfigParserInterface $parser): void
    {
        foreach ($this->getSupportedFormats($parser) as $format) {
            $this->parsers[$format] = $parser;
        }
    }

    public function load(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Config file not found: $filePath");
        }

        $format = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!isset($this->parsers[$format])) {
            throw new InvalidConfigFormatException("Unsupported config format: $format");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Unable to read config file: $filePath");
        }

        return $this->parsers[$format]->parse($content);
    }

    private function getSupportedFormats(ConfigParserInterface $parser): array
    {
        $formats = [];
        if ($parser->supports('json')) $formats[] = 'json';
        if ($parser->supports('yaml')) $formats[] = 'yaml';
        if ($parser->supports('yml')) $formats[] = 'yml';
        return $formats;
    }

    public function getSupportedFormatsList(): array
    {
        return array_keys($this->parsers);
    }
}