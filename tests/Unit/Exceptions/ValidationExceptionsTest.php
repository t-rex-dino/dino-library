<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\ConfigValidationException;
use Dino\Exceptions\ValidatorNotFoundException;
use Dino\Core\ErrorCodes;

class ValidationExceptionsTest extends TestCase
{
    public function testValidationException()
    {
        $exception = new ConfigValidationException('email', ['Invalid format']);
        $this->assertEquals(ErrorCodes::CONFIG_VALIDATION_FAILED, $exception->getErrorCode());
        $this->assertStringContainsString('email', $exception->getErrorMessage());
    }

    public function testValidatorNotFoundException()
    {
        $exception = new ValidatorNotFoundException('EmailValidator');
        $this->assertEquals(ErrorCodes::VALIDATOR_NOT_FOUND, $exception->getErrorCode());
        $this->assertStringContainsString('EmailValidator', $exception->getErrorMessage());
    }
}
