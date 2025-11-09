<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\ConfigHandler;
use Dino\Exceptions\ConfigurationException;

final class ConfigHandlerTest extends TestCase
{
    public function testSetAndGetConfig(): void
    {
        $config = new ConfigHandler();
        $config->set('key', 'value');

        $this->assertSame('value', $config->get('key'));
    }

    public function testHasConfig(): void
    {
        $config = new ConfigHandler();
        $config->set('key', 'value');

        $this->assertTrue($config->has('key'));
        $this->assertFalse($config->has('missing'));
    }

    public function testGetMissingConfigThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $config = new ConfigHandler();
        $config->get('unknown');
    }
}
