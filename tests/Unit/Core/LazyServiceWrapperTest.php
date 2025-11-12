<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Dino\Core\LazyServiceWrapper;

class HeavyService {
    public function compute(): string {
        return "Heavy computation done!";
    }
}

final class LazyServiceWrapperTest extends TestCase
{
    public function testServiceIsNotInitializedInitially(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new HeavyService());
        $this->assertFalse($wrapper->isInitialized());
    }

    public function testServiceIsCreatedOnFirstCall(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new HeavyService());
        $service = $wrapper->create();
        
        $this->assertInstanceOf(HeavyService::class, $service);
        $this->assertTrue($wrapper->isInitialized());
    }

    public function testServiceIsSingletonAfterInitialization(): void
    {
        $wrapper = new LazyServiceWrapper(fn() => new HeavyService());
        $first = $wrapper->create();
        $second = $wrapper->create();

        $this->assertSame($first, $second);
    }
}
