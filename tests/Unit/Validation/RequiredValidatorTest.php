<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\RequiredValidator;
use Dino\Exceptions\ConfigValidationException;
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
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate(null, ['configKey' => 'test.key']);
    }

    public function testEmptyStringThrowsException(): void
    {
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate('', ['configKey' => 'test.key']);
    }

    public function testExceptionContainsContext(): void
    {
        try {
            $this->validator->validate(null, ['configKey' => 'app.name', 'rule' => 'required']);
            $this->fail('Expected ConfigValidationException was not thrown');
        } catch (ConfigValidationException $e) {
            $context = $e->getContext();
            $this->assertEquals('app.name', $context['configKey']);
            $this->assertEquals('required', $context['rule']);
            $this->assertArrayHasKey('configKey', $context);
        }
    }
}