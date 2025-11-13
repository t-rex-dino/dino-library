<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\Config\Parsers\JsonConfigParser;
use Dino\Exceptions\ConfigParserException;

class JsonConfigParserTest extends TestCase
{
    public function testValidJson(): void
    {
        $parser = new JsonConfigParser();
        $json = '{"app":{"name":"Dino"}}';
        $result = $parser->parse($json);
        $this->assertEquals(['app' => ['name' => 'Dino']], $result);
    }

    public function testInvalidJson(): void
    {
        $this->expectException(ConfigParserException::class);
        $parser = new JsonConfigParser();
        $parser->parse('{invalid}');
    }
}
