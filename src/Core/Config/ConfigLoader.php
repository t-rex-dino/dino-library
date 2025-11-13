<?php
declare(strict_types=1);

namespace Dino\Core\Config;

use Dino\Contracts\ConfigLoaderInterface;
use Dino\Contracts\ConfigParserInterface;
use Dino\Exceptions\ConfigNotFoundException;
use Dino\Exceptions\ConfigParserException;
use Dino\Exceptions\ConfigResolutionException;
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
            throw new ConfigNotFoundException(
                $filePath,
                ['reason' => 'File does not exist']
            );
        }

        $format = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!isset($this->parsers[$format])) {
            throw new ConfigParserException(
                $filePath,
                ['reason' => "Unsupported config format: $format"]
            );
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new ConfigResolutionException(
                $filePath,
                ['reason' => 'Unable to read config file']
            );
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
