<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\ConfigValidationException;
use Dino\Exceptions\ConfigNotFoundException;
use Dino\Exceptions\ConfigParserException;
use Dino\Core\ErrorCodes;
use Dino\Core\ErrorMessageFormatter;

class ConfigExceptionsTest extends TestCase
{
    public function testConfigValidationException()
    {
        $errors = ['app.port' => ['Must be integer']];
        $exception = new ConfigValidationException('app.port', ['config_file' => 'config/app.php']);
        $this->assertEquals(ErrorCodes::CONFIG_VALIDATION_FAILED, $exception->getErrorCode());
        $formatted = ErrorMessageFormatter::formatValidationErrors($errors);
        $this->assertStringContainsString('app.port', $formatted);
    }

    public function testConfigNotFoundException()
    {
        $exception = new ConfigNotFoundException('app.name');
        $this->assertEquals(ErrorCodes::CONFIG_KEY_NOT_FOUND, $exception->getErrorCode());
        $this->assertStringContainsString('app.name', $exception->getErrorMessage());
    }

    public function testConfigParserException()
    {
        $exception = new ConfigParserException('config.yaml');
        $this->assertEquals(ErrorCodes::CONFIG_PARSER_ERROR, $exception->getErrorCode());
        $this->assertStringContainsString('config.yaml', $exception->getErrorMessage());
    }
}
