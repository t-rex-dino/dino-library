<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\ServiceNotFoundException;
use Dino\Core\ErrorCodes;

class ContextAwareExceptionTest extends TestCase
{
    public function testFormattedMessageIncludesContext()
    {
        $exception = new ServiceNotFoundException('logger', ['attempt' => 1]);
        $formatted = $exception->getFormattedMessage();

        $this->assertStringContainsString('SERVICE_100', $formatted);
        $this->assertStringContainsString('logger', $formatted);
        $this->assertStringContainsString('Context', $formatted);
    }

    public function testSeverityMapping()
    {
        $exception = new ServiceNotFoundException('logger');
        $this->assertEquals(
            ErrorCodes::getSeverity(ErrorCodes::SERVICE_NOT_FOUND),
            $exception->getSeverity()
        );
    }
}
