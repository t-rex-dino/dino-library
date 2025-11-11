<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\TypeValidator;
use Dino\Exceptions\ValidationException;
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
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("must be of type 'string'");

        $this->validator->validate(123, ['configKey' => 'app.name', 'expectedType' => 'string']);
    }

    public function testExceptionContainsContext(): void
    {
        try {
            $this->validator->validate([], ['configKey' => 'app.port', 'expectedType' => 'int', 'rule' => 'type:int']);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertEquals('app.port', $e->getConfigKey());
            $this->assertEquals('type:int', $e->getRule());
            $this->assertArrayHasKey('expectedType', $e->getContext());
        }
    }
}
