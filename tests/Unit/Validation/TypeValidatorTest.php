<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\TypeValidator;
use Dino\Exceptions\ConfigValidationException;
use PHPUnit\Framework\TestCase;

class TypeValidatorTest extends TestCase
{
    private TypeValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new TypeValidator();
    }

    public function testSupportsTypeRule(): void
    {
        $this->assertTrue($this->validator->supports('type:string'));
        $this->assertTrue($this->validator->supports('type:int'));
        $this->assertFalse($this->validator->supports('required'));
    }

    public function testValidStringPassesValidation(): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate('hello', ['expectedType' => 'string']);
    }

    public function testInvalidTypeThrowsException(): void
    {
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate(123, ['configKey' => 'app.name', 'expectedType' => 'string']);
    }
    
    public function testExceptionContainsContext(): void
    {
        try {
            $this->validator->validate('not_an_int', ['configKey' => 'app.port', 'rule' => 'type:int']);
            $this->fail('Expected ConfigValidationException was not thrown');
        } catch (ConfigValidationException $e) {
            $context = $e->getContext();
            $this->assertEquals('app.port', $context['configKey']);
            $this->assertEquals('type', $context['rule']);
            $this->assertArrayHasKey('configKey', $context);
        }
    }
}
