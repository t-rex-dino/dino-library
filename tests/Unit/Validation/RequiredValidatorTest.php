<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\RequiredValidator;
use Dino\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class RequiredValidatorTest extends TestCase
{
    private RequiredValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RequiredValidator();
    }

    public function testSupportsRequiredRule(): void
    {
        $this->assertTrue($this->validator->supports('required'));
        $this->assertFalse($this->validator->supports('type:string'));
    }

    public function testValidValuePassesValidation(): void
    {
        $this->expectNotToPerformAssertions();
        
        $this->validator->validate('valid value');
        $this->validator->validate(0);
        $this->validator->validate(false);
        $this->validator->validate([]);
    }

    public function testNullValueThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("is required and cannot be empty");

        $this->validator->validate(null, ['configKey' => 'test.key']);
    }

    public function testEmptyStringThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("is required and cannot be empty");

        $this->validator->validate('', ['configKey' => 'test.key']);
    }

    public function testExceptionContainsContext(): void
    {
        try {
            $this->validator->validate(null, ['configKey' => 'app.name', 'rule' => 'required']);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertEquals('app.name', $e->getConfigKey());
            $this->assertEquals('required', $e->getRule());
            $this->assertArrayHasKey('configKey', $e->getContext());
        }
    }
}