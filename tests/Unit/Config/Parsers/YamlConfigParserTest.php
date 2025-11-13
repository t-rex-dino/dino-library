<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\Config\Parsers\YamlConfigParser;
use Dino\Exceptions\ConfigParserException;

class YamlConfigParserTest extends TestCase
{
    public function testValidYaml(): void
    {
        $parser = new YamlConfigParser();
        $yaml = "app:\n  name: Dino";
        $result = $parser->parse($yaml);
        $this->assertEquals(['app' => ['name' => 'Dino']], $result);
    }

    public function testInvalidYaml(): void
    {
        $this->expectException(ConfigParserException::class);
        $parser = new YamlConfigParser();
        $parser->parse("app: name: Dino:"); // malformed
    }
}
