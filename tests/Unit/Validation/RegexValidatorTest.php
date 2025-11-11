<?php

namespace Dino\Tests\Unit\Validation;

use Dino\Validation\Rules\RegexValidator;
use Dino\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    private RegexValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RegexValidator();
    }

    public function testSupportsRegexRule(): void
    {
        $this->assertTrue($this->validator->supports('regex'));
        $this->assertFalse($this->validator->supports('required'));
    }

    public function testValidValueMatchesPattern(): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate("user@example.com", [
            'pattern' => '/^[^@]+@[^@]+\.[^@]+$/',
            'configKey' => 'app.email'
        ]);
    }

    public function testInvalidValueThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("does not match the required pattern");

        $this->validator->validate("invalid-email", [
            'pattern' => '/^[^@]+@[^@]+\.[^@]+$/',
            'configKey' => 'app.email'
        ]);
    }

    public function testMissingPatternThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Regex pattern is required");

        $this->validator->validate("test", ['configKey' => 'app.email']);
    }
}
