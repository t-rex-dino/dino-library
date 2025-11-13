<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\RangeValidator;
use Dino\Exceptions\ConfigValidationException;
use PHPUnit\Framework\TestCase;

class RangeValidatorTest extends TestCase
{
    private RangeValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RangeValidator();
    }

    public function testSupportsRangeRule(): void
    {
        $this->assertTrue($this->validator->supports('range'));
        $this->assertFalse($this->validator->supports('required'));
    }

    public function testValidValueWithinRangePassesValidation(): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate(10, ['min' => 1, 'max' => 20]);
    }

    public function testValueBelowRangeThrowsException(): void
    {
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate(-5, ['configKey' => 'app.port', 'min' => 1, 'max' => 100]);
    }

    public function testValueAboveRangeThrowsException(): void
    {
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate(200, ['configKey' => 'app.port', 'min' => 1, 'max' => 100]);
    }

    public function testNonNumericValueThrowsException(): void
    {
        $this->expectException(ConfigValidationException::class);

        $this->validator->validate("abc", ['configKey' => 'app.port', 'min' => 1, 'max' => 100]);
    }
}
