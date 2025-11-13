<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\UnresolvableParameterException;
use Dino\Exceptions\MissingTypeHintException;
use Dino\Exceptions\InterfaceNotBoundException;
use Dino\Core\ErrorCodes;

class DIExceptionsTest extends TestCase
{
    public function testUnresolvableParameterException()
    {
        $exception = new UnresolvableParameterException('serviceProvider');
        $this->assertEquals(ErrorCodes::DI_UNRESOLVABLE_PARAMETER, $exception->getErrorCode());
        $this->assertStringContainsString('serviceProvider', $exception->getErrorMessage());
    }

    public function testMissingTypeHintException()
    {
        $exception = new MissingTypeHintException('logger');
        $this->assertEquals(ErrorCodes::DI_MISSING_TYPE_HINT, $exception->getErrorCode());
        $this->assertStringContainsString('logger', $exception->getErrorMessage());
    }

    public function testInterfaceNotBoundException()
    {
        $exception = new InterfaceNotBoundException('LoggerInterface');
        $this->assertEquals(ErrorCodes::DI_INTERFACE_NOT_BOUND, $exception->getErrorCode());
        $this->assertStringContainsString('LoggerInterface', $exception->getErrorMessage());
    }
}
