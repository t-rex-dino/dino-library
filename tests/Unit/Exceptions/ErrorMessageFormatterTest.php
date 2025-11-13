<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\ConfigValidationException;
use Dino\Core\ErrorMessageFormatter;

class ErrorMessageFormatterTest extends TestCase
{
    public function testFormatReturnsStandardizedMessage()
    {
        $exception = new ConfigValidationException('app.port');
        $formatted = ErrorMessageFormatter::format($exception);

        $this->assertStringContainsString('CONFIG_200', $formatted);
        $this->assertStringContainsString('app.port', $formatted);
    }

    public function testCreateDetailsReturnsArray()
    {
        $exception = new ConfigValidationException('app.port');
        $details = ErrorMessageFormatter::createDetails($exception);

        $this->assertArrayHasKey('code', $details);
        $this->assertArrayHasKey('message', $details);
        $this->assertArrayHasKey('severity', $details);
        $this->assertArrayHasKey('context', $details);
        $this->assertArrayHasKey('timestamp', $details);
    }
}
