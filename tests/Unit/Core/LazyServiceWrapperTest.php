<?php
declare(strict_types=1);

namespace Dino\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Dino\Core\LazyServiceWrapper;

final class LazyServiceWrapperTest extends TestCase
{
    public function testServiceIsNotInitializedInitially(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new class {
            public function compute(): string {
                return "Heavy computation done!";
            }
        });
        $this->assertFalse($wrapper->isInitialized());
    }

    public function testServiceIsCreatedOnFirstCall(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new class {
            public function compute(): string {
                return "Heavy computation done!";
            }
        });
        $service = $wrapper->create();
        
        $this->assertTrue($wrapper->isInitialized());
    }

    public function testServiceIsSingletonAfterInitialization(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new class {
            public function compute(): string {
                return "Heavy computation done!";
            }
        });
        $first = $wrapper->create();
        $second = $wrapper->create();

        $this->assertSame($first, $second);
    }
}